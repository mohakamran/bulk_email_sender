<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailJob;
use App\Mail\BulkEmailMailable;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BulkEmailController extends Controller
{
    public function index()
    {
        return view('bulk-email');
    }

public function data(Request $request)
{
    // Start query
    $query = EmailJob::query()->latest();

    // --- SEARCH ---
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('university', 'LIKE', "%{$search}%")
              ->orWhere('research', 'LIKE', "%{$search}%");
        });
    }

    // --- STATUS FILTER ---
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    // Pagination with query string preserved
    $emailJobs = $query->paginate(50)->withQueryString();

    return view('bulk-email-data', compact('emailJobs'));
}


    public function store(Request $request)
    {
        $request->validate([
            'emails' => 'required|string',
            'names' => 'required|string',
            'universities' => 'required|string',
            'research_fields' => 'required|string',
            'cv' => 'required|file|mimes:pdf|max:10240',
        ]);

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        }

        $emails = $this->parseTextArea($request->emails);
        $names = $this->parseTextArea($request->names);
        $universities = $this->parseTextArea($request->universities);
        $researchFields = $this->parseTextArea($request->research_fields);

        // Find the maximum count among all arrays
        $maxCount = max(count($emails), count($names), count($universities), count($researchFields));
        
        // Pad arrays to match the maximum count
        $emails = array_pad($emails, $maxCount, 'N/A');
        $names = array_pad($names, $maxCount, 'N/A');
        $universities = array_pad($universities, $maxCount, 'N/A');
        $researchFields = array_pad($researchFields, $maxCount, 'N/A');

        $createdCount = 0;
        $sentCount = 0;
        $failedCount = 0;
        $delayInSeconds = 0;
        $now = now();

        foreach ($emails as $index => $email) {
            if ($email === 'N/A') continue; // Skip if email is N/A

            // Create email job record
            $emailJob = EmailJob::create([
                'name' => $names[$index],
                'email' => $email,
                'university' => $universities[$index],
                'research' => $researchFields[$index],
                'cv_path' => $cvPath,
                'scheduled_at' => $now->copy()->addSeconds($delayInSeconds),
                'status' => 'pending',
            ]);

            // Send email immediately with delay
            try {
                // Add delay between emails
                if ($delayInSeconds > 0) {
                    sleep(min(5, $delayInSeconds)); // Max 5 seconds delay to avoid timeout
                }
                
                Mail::to($email)->send(new BulkEmailMailable(
                    $names[$index],
                    $universities[$index],
                    $researchFields[$index],
                    $cvPath
                ));
                
                $emailJob->update(['status' => 'sent']);
                $sentCount++;
                $delayInSeconds += 5; // 5 second delay between emails

            } catch (\Exception $e) {
                $emailJob->update(['status' => 'failed']);
                $failedCount++;
                $createdCount++;
                
                // Log the error for debugging
                \Log::error('Email sending failed: ' . $e->getMessage());
            }
        }

        $message = "Successfully sent {$sentCount} emails" . ($failedCount > 0 ? " ({$failedCount} failed)" : "") . " and saved to database!";

        return back()->with('success', $message);
    }

    public function sendIndividual(Request $request, $id)
    {
        $emailJob = EmailJob::findOrFail($id);
        
        // Log the attempt
        \Log::info("Attempting to send individual email to ID: {$id}, Email: {$emailJob->email}");
        
        try {
            // Send the email
            Mail::to($emailJob->email)->send(new BulkEmailMailable(
                $emailJob->name,
                $emailJob->university,
                $emailJob->research,
                $emailJob->cv_path
            ));
            
            // Update status
            $emailJob->update(['status' => 'sent']);
            
            \Log::info("Email sent successfully to {$emailJob->name}");
            
            return back()->with('success', "Email sent successfully to {$emailJob->name} ({$emailJob->email})");
            
        } catch (\Exception $e) {
            $emailJob->update(['status' => 'failed']);
            \Log::error("Email sending failed for {$emailJob->name}: " . $e->getMessage());
            return back()->with('error', "Failed to send email to {$emailJob->name}: " . $e->getMessage());
        }
    }

    private function parseTextArea($text)
    {
        return array_filter(array_map('trim', preg_split('/[\n,]+/', $text)));
    }
}
