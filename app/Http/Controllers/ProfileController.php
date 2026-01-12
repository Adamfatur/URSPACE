<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Thread;
use App\Models\UserExperience;
use App\Models\UserEducation;
use App\Models\UserSkill;
use App\Models\UserCertification;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request, User $user)
    {
        $tab = $request->query('tab', 'threads');
        $content = collect();

        $user->load(['experiences', 'educations', 'skills', 'certifications']);

        if ($tab === 'threads') {
            $content = $user->threads()->withCount(['likes', 'posts'])->latest()->paginate(10);
        } elseif ($tab === 'replies') {
            $content = $user->posts()->with('thread')->latest()->paginate(10);
        } elseif ($tab === 'likes') {
            $content = Thread::whereHas('likes', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->withCount(['likes', 'posts'])->latest()->paginate(10);
        }

        return view('profile.show', compact('user', 'content', 'tab'));
    }

    public function edit()
    {
        $user = auth()->user();
        $user->load(['experiences', 'educations', 'skills', 'certifications']);
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
            // Academic
            'nim' => 'nullable|string|max:50',
            'program_studi' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'angkatan' => 'nullable|integer|min:1990|max:2100',
            // Professional
            'headline' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            // Open to Work
            'is_open_to_work' => 'boolean',
            'open_to_work_types' => 'nullable|array',
        ]);

        $data = $request->only([
            'name',
            'bio',
            'nim',
            'program_studi',
            'fakultas',
            'angkatan',
            'headline',
            'location',
            'website',
            'linkedin_url',
            'github_url',
            'is_open_to_work',
            'open_to_work_types'
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Check if profile is now complete
        if (!empty($data['nim']) && !empty($data['program_studi']) && !empty($data['angkatan']) && !$user->profile_completed_at) {
            $data['profile_completed_at'] = now();
        }

        $user->update($data);

        return redirect()->route('profile.show', $user->username)->with('success', 'Profil berhasil diperbarui!');
    }

    public function follow(Request $request, User $user)
    {
        $follower = $request->user();

        if ($follower->following()->where('followed_id', $user->id)->exists()) {
            $follower->following()->detach($user->id);
        } else {
            $follower->following()->attach($user->id);
        }

        return back();
    }

    // Experience CRUD
    public function storeExperience(Request $request)
    {
        $request->validate([
            'company' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string|max:2000',
        ]);

        auth()->user()->experiences()->create($request->all());

        return back()->with('success', 'Pengalaman berhasil ditambahkan!');
    }

    public function updateExperience(Request $request, UserExperience $experience)
    {
        if ($experience->user_id !== auth()->id())
            abort(403);

        $request->validate([
            'company' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string|max:2000',
        ]);

        $experience->update($request->all());

        return back()->with('success', 'Pengalaman berhasil diperbarui!');
    }

    public function destroyExperience(UserExperience $experience)
    {
        if ($experience->user_id !== auth()->id())
            abort(403);
        $experience->delete();
        return back()->with('success', 'Pengalaman berhasil dihapus!');
    }

    // Education CRUD
    public function storeEducation(Request $request)
    {
        $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'nullable|string|max:100',
            'field_of_study' => 'nullable|string|max:255',
            'start_year' => 'required|integer|min:1990|max:2100',
            'end_year' => 'nullable|integer|min:1990|max:2100',
            'is_current' => 'boolean',
            'description' => 'nullable|string|max:2000',
            'activities' => 'nullable|string|max:2000',
        ]);

        auth()->user()->educations()->create($request->all());

        return back()->with('success', 'Pendidikan berhasil ditambahkan!');
    }

    public function updateEducation(Request $request, UserEducation $education)
    {
        if ($education->user_id !== auth()->id())
            abort(403);

        $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'nullable|string|max:100',
            'field_of_study' => 'nullable|string|max:255',
            'start_year' => 'required|integer|min:1990|max:2100',
            'end_year' => 'nullable|integer|min:1990|max:2100',
            'is_current' => 'boolean',
            'description' => 'nullable|string|max:2000',
            'activities' => 'nullable|string|max:2000',
        ]);

        $education->update($request->all());

        return back()->with('success', 'Pendidikan berhasil diperbarui!');
    }

    public function destroyEducation(UserEducation $education)
    {
        if ($education->user_id !== auth()->id())
            abort(403);
        $education->delete();
        return back()->with('success', 'Pendidikan berhasil dihapus!');
    }

    // Skills
    public function storeSkill(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        auth()->user()->skills()->firstOrCreate(['name' => $request->name]);

        return back()->with('success', 'Skill berhasil ditambahkan!');
    }

    public function destroySkill(UserSkill $skill)
    {
        if ($skill->user_id !== auth()->id())
            abort(403);
        $skill->delete();
        return back()->with('success', 'Skill berhasil dihapus!');
    }

    // Certifications CRUD
    public function storeCertification(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:255',
        ]);

        auth()->user()->certifications()->create($request->all());

        return back()->with('success', 'Sertifikasi berhasil ditambahkan!');
    }

    public function updateCertification(Request $request, UserCertification $certification)
    {
        if ($certification->user_id !== auth()->id())
            abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:255',
        ]);

        $certification->update($request->all());

        return back()->with('success', 'Sertifikasi berhasil diperbarui!');
    }

    public function destroyCertification(UserCertification $certification)
    {
        if ($certification->user_id !== auth()->id())
            abort(403);
        $certification->delete();
        return back()->with('success', 'Sertifikasi berhasil dihapus!');
    }

    // Toggle Open to Work
    public function toggleOpenToWork(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'is_open_to_work' => !$user->is_open_to_work,
            'open_to_work_types' => $request->open_to_work_types ?? null,
        ]);

        return back()->with('success', $user->is_open_to_work ? 'Status Open to Work diaktifkan!' : 'Status Open to Work dinonaktifkan!');
    }
}
