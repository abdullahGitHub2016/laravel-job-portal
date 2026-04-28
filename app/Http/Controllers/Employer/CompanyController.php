<?php
namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function edit(): Response
    {
        $profile = auth()->user()->employerProfile;

        if (! $profile) {
            $profile = auth()->user()->employerProfile()->create([
                'company_name' => auth()->user()->name . "'s Company",
                'slug'         => Str::slug(auth()->user()->name . '-' . auth()->id()),
            ]);
        }

        return Inertia::render('Employer/Company', [
            'profile'    => $profile,
            'industries' => Industry::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name'     => ['required', 'string', 'max:200'],
            'company_overview' => ['nullable', 'string'],
            'company_type'     => ['nullable', 'string'],
            'company_size'     => ['nullable', 'integer', 'min:1'],
            'founded_year'     => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'website'          => ['nullable', 'url'],
            'address'          => ['nullable', 'string', 'max:300'],
            'district'         => ['nullable', 'string', 'max:100'],
            'industry_id'      => ['nullable', 'exists:industries,id'],
        ]);

        auth()->user()->employerProfile->update($data);

        return back()->with('success', 'Company profile updated.');
    }

    public function updateLogo(Request $request): RedirectResponse
    {
        $request->validate(['logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);

        $profile = auth()->user()->employerProfile;

        if ($profile->logo) {
            Storage::disk('public')->delete($profile->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $profile->update(['logo' => $path]);

        return back()->with('success', 'Logo updated.');
    }
}
