<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Mail\AppointmentConfirmedMail;
use App\Models\Appointment;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    /**
     * Check whether real Razorpay keys are configured.
     * Placeholder or empty keys trigger demo mode.
     */
    private function isRazorpayConfigured(): bool
    {
        $key    = config('services.razorpay.key', '');
        $secret = config('services.razorpay.secret', '');

        return !empty($key)
            && !empty($secret)
            && str_starts_with($key, 'rzp_')
            && !str_contains($key, 'YourKey')
            && !str_contains($key, 'your_key')
            && strlen($key) > 15;
    }

    /**
     * Initiate payment.
     * — Real keys configured  → create Razorpay order → checkout page with Razorpay JS
     * — Placeholder/no keys   → demo mode → checkout page with "Simulate Payment" button
     */
    public function initiate(AppointmentRequest $request)
    {
        $professional = Professional::with(['user', 'category'])->findOrFail($request->professional_id);
        $amountPaise  = (int) ($professional->consultation_fee * 100);

        // ── DEMO MODE ──────────────────────────────────────────────────────────
        if (!$this->isRazorpayConfigured()) {
            session([
                'booking_intent' => [
                    'professional_id'   => $request->professional_id,
                    'appointment_date'  => $request->appointment_date,
                    'time_slot'         => $request->time_slot,
                    'notes'             => $request->notes,
                    'fee'               => $professional->consultation_fee,
                    'razorpay_order_id' => 'demo_' . uniqid(),
                    'demo_mode'         => true,
                ],
            ]);

            return view('payments.checkout', [
                'professional'   => $professional,
                'order'          => (object)['id' => 'DEMO-' . strtoupper(uniqid())],
                'razorpayKey'    => null,               // null = demo mode in view
                'amount'         => $professional->consultation_fee,
                'amountPaise'    => $amountPaise,
                'appointmentDate'=> $request->appointment_date,
                'timeSlot'       => $request->time_slot,
                'user'           => Auth::user(),
                'demoMode'       => true,
            ]);
        }

        // ── LIVE RAZORPAY MODE ─────────────────────────────────────────────────
        try {
            $razorpay = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $order = $razorpay->order->create([
                'amount'          => $amountPaise,
                'currency'        => 'INR',
                'receipt'         => 'rcpt_' . uniqid(),
                'payment_capture' => 1,
                'notes'           => [
                    'professional' => $professional->user->name,
                    'date'         => $request->appointment_date,
                    'slot'         => $request->time_slot,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return back()->with('error', 'Payment gateway error: ' . $e->getMessage());
        }

        session([
            'booking_intent' => [
                'professional_id'   => $request->professional_id,
                'appointment_date'  => $request->appointment_date,
                'time_slot'         => $request->time_slot,
                'notes'             => $request->notes,
                'fee'               => $professional->consultation_fee,
                'razorpay_order_id' => $order->id,
                'demo_mode'         => false,
            ],
        ]);

        return view('payments.checkout', [
            'professional'   => $professional,
            'order'          => $order,
            'razorpayKey'    => config('services.razorpay.key'),
            'amount'         => $professional->consultation_fee,
            'amountPaise'    => $amountPaise,
            'appointmentDate'=> $request->appointment_date,
            'timeSlot'       => $request->time_slot,
            'user'           => Auth::user(),
            'demoMode'       => false,
        ]);
    }

    /**
     * Handle Razorpay success callback — verify signature then create appointment.
     */
    public function success(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $intent = session('booking_intent');

        if (!$intent || $intent['razorpay_order_id'] !== $request->razorpay_order_id) {
            return redirect()->route('professionals.index')
                ->with('error', 'Invalid payment session. Please try booking again.');
        }

        // Verify signature (live mode only)
        if (!($intent['demo_mode'] ?? false)) {
            try {
                $razorpay = new \Razorpay\Api\Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );
                $razorpay->utility->verifyPaymentSignature([
                    'razorpay_order_id'   => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature'  => $request->razorpay_signature,
                ]);
            } catch (\Exception $e) {
                Log::error('Razorpay signature verification failed: ' . $e->getMessage());
                return redirect()->route('payments.failed')->with('error', 'Payment verification failed.');
            }
        }

        $appointment = $this->createAppointment($intent, [
            'razorpay_order_id'   => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature'  => $request->razorpay_signature,
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', '🎉 Payment successful! Your appointment has been booked.');
    }

    /**
     * Demo/simulate payment — creates appointment without real payment.
     * Only works when Razorpay keys are not configured.
     */
    public function simulate(Request $request)
    {
        // Block if real keys are configured
        if ($this->isRazorpayConfigured()) {
            return redirect()->route('professionals.index')
                ->with('error', 'Simulation not available in live payment mode.');
        }

        $intent = session('booking_intent');

        if (!$intent || !($intent['demo_mode'] ?? false)) {
            return redirect()->route('professionals.index')
                ->with('error', 'Invalid session. Please try booking again.');
        }

        $appointment = $this->createAppointment($intent, [
            'razorpay_order_id'   => $intent['razorpay_order_id'],
            'razorpay_payment_id' => 'demo_pay_' . uniqid(),
            'razorpay_signature'  => 'demo_sig_' . uniqid(),
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', '✅ Demo booking confirmed! Add real Razorpay keys to enable live payments.');
    }

    /**
     * Shared helper: create appointment + send email + clear session.
     */
    private function createAppointment(array $intent, array $paymentData): Appointment
    {
        $appointment = Appointment::create([
            'user_id'             => Auth::id(),
            'professional_id'     => $intent['professional_id'],
            'appointment_date'    => $intent['appointment_date'],
            'time_slot'           => $intent['time_slot'],
            'notes'               => $intent['notes'] ?? null,
            'fee'                 => $intent['fee'],
            'status'              => 'pending',
            'payment_status'      => 'paid',
            'razorpay_order_id'   => $paymentData['razorpay_order_id'],
            'razorpay_payment_id' => $paymentData['razorpay_payment_id'],
            'razorpay_signature'  => $paymentData['razorpay_signature'],
        ]);

        $appointment->load(['user', 'professional.user', 'professional.category']);
        session()->forget('booking_intent');

        try {
            Mail::to(Auth::user()->email)->queue(new AppointmentConfirmedMail($appointment));
        } catch (\Exception $e) {
            // Fail silently — appointment is still created
        }

        return $appointment;
    }

    /**
     * Payment failed page.
     */
    public function failed(Request $request)
    {
        session()->forget('booking_intent');
        return view('payments.failed');
    }
}
