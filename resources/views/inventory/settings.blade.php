<?php
namespace App\Http\Controllers;  

use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function index()
    {
        return view('settings'); // or your actual settings view
    }
}
?>

@extends('layouts.app')

@section('content')
    @include('layouts.nav')
    <div class="flex justify-center items-center min-h-screen bg-gray-100" style="background: url('/your-bg.jpg') no-repeat center center fixed; background-size: cover;">
        <div class="bg-white p-8 rounded shadow mx-auto" style="max-width: 500px; width: 100%;">
            {{-- Settings Heading --}}
            @include('partials.settings-heading')

            {{-- Phone Verification --}}
            <div class="card shadow mb-6">
                <div class="card-body">
                    <h3 class="card-title mb-4">Verify Employee Phone Number</h3>
                    <form id="verificationForm" autocomplete="off">
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input type="text" id="phoneNumber" class="form-control" placeholder="+27721234567" required>
                        </div>
                        <button type="button" class="btn btn-primary mb-3 w-full" onclick="sendCode()">Send Verification Code</button>
                        <div id="otpSection" style="display: none;">
                            <div class="mb-3">
                                <label for="otpCode" class="form-label">Enter 6-digit Code</label>
                                <input type="text" id="otpCode" class="form-control" maxlength="6" required>
                            </div>
                            <button type="submit" class="btn btn-success w-full">Verify</button>
                        </div>
                        <div id="message" class="mt-3"></div>
                        <div id="timer" class="mb-2 text-muted"></div>
                    </form>
                </div> <!-- end of card-body -->
            </div> <!-- end of card -->

            <a href="{{ route('settings.profile') }}"
               class="btn bg-blue-200 text-black font-semibold shadow hover:bg-blue-300 transition px-4 py-2 rounded mb-4 w-full text-center d-block">
                Go to Profile
            </a>

            {{-- Profile/Password/Appearance Settings --}}
            <div class="mb-6">
                {{-- Uncomment these if you have the components --}}
                {{-- @livewire('settings.profile') --}}
                {{-- @livewire('settings.password') --}}
            </div>

            {{-- Appearance Settings --}}
            <!-- <div>
                @livewire('settings.appearance')
            </div> -->
        </div>
    </div>

    {{-- Your JS for phone verification --}}
    <script>
    let attempts = 0;
    let maxAttempts = 3;
    let timer;
    let timeLeft = 300; // 5 minutes in seconds

    function startTimer() {
        clearInterval(timer);
        timeLeft = 300;
        updateTimerDisplay();
        timer = setInterval(() => {
            timeLeft--;
            updateTimerDisplay();
            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById('message').textContent = "Verification time expired. Please request a new code.";
                document.getElementById('message').className = "text-danger mt-3";
                document.getElementById('otpSection').style.display = 'none';
                attempts = 0;
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const timerDiv = document.getElementById('timer');
        if (timerDiv) {
            const min = Math.floor(timeLeft / 60);
            const sec = timeLeft % 60;
            timerDiv.textContent = `Time left: ${min}:${sec.toString().padStart(2, '0')}`;
        }
    }

    async function sendCode() {
        const phoneNumber = document.getElementById('phoneNumber').value;
        const messageElement = document.getElementById('message');
        const otpSection = document.getElementById('otpSection');
        const timerDiv = document.getElementById('timer');

        if (!phoneNumber) {
            messageElement.textContent = "Please enter a phone number.";
            messageElement.className = "text-danger mt-3";
            return;
        }

        // Simulate backend response
        messageElement.textContent = "A 6-digit code has been sent to your phone. Please enter it below.";
        messageElement.className = "text-info mt-3";
        otpSection.style.display = 'block';
        attempts = 0;
        startTimer();
        timerDiv.style.display = 'block';
        console.log("Simulated OTP sent to " + phoneNumber + " (123456)");
    }

    document.getElementById('verificationForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const otpCode = document.getElementById('otpCode').value;
        const messageElement = document.getElementById('message');
        const timerDiv = document.getElementById('timer');

        if (timeLeft <= 0) {
            messageElement.textContent = "Verification time expired. Please request a new code.";
            messageElement.className = "text-danger mt-3";
            document.getElementById('otpSection').style.display = 'none';
            return;
        }

        if (!otpCode || otpCode.length !== 6) {
            messageElement.textContent = "Please enter a valid 6-digit code.";
            messageElement.className = "text-danger mt-3";
            return;
        }

        attempts++;
        if (otpCode === "123456") { // Replace with actual OTP comparison from backend
            messageElement.textContent = "Verification successful!";
            messageElement.className = "text-success mt-3";
            clearInterval(timer);
        } else {
            if (attempts >= maxAttempts) {
                messageElement.textContent = "Maximum attempts reached. Please request a new code.";
                messageElement.className = "text-danger mt-3";
                document.getElementById('otpSection').style.display = 'none';
                clearInterval(timer);
            } else {
                messageElement.textContent = `Invalid code. You have ${maxAttempts - attempts} attempt(s) left.`;
                messageElement.className = "text-danger mt-3";
            }
        }
    });
    </script>
@endsection

