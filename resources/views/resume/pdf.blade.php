<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 0; }
  .container { padding: 40px; }
  h1 { font-size: 24px; margin: 0; }
  h2 { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-top: 24px; }
  .meta { color: #64748b; font-size: 11px; margin-top: 4px; }
  .section-item { margin-bottom: 14px; }
  .flex { display: flex; justify-content: space-between; }
  .skill { display: inline-block; padding: 3px 10px; border: 1px solid #cbd5e1; border-radius: 999px; margin: 3px; font-size: 11px; }
</style>
</head>
<body>
<div class="container">

  <h1>{{ $profile->user->name }}</h1>

  @if($profile->headline)
  <p style="font-size:14px;color:#475569;margin-top:4px;">{{ $profile->headline }}</p>
  @endif

  <p class="meta">
    {{ $profile->user->email }}
    @if($profile->user->phone)
    &nbsp;&middot;&nbsp;{{ $profile->user->phone }}
    @endif
    @if($profile->district)
    &nbsp;&middot;&nbsp;{{ $profile->district }}, Bangladesh
    @endif
  </p>

  @if($profile->bio)
  <h2>Summary</h2>
  <p>{{ $profile->bio }}</p>
  @endif

  @if($profile->workExperiences->count())
  <h2>Experience</h2>
  @foreach($profile->workExperiences->sortByDesc('start_date') as $exp)
  <div class="section-item">
    <div class="flex">
      <strong>{{ $exp->job_title }} - {{ $exp->company_name }}</strong>
      <span class="meta">
        {{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }}
        @if($exp->is_current)
        - Present
        @else
        - {{ \Carbon\Carbon::parse($exp->end_date)->format('M Y') }}
        @endif
      </span>
    </div>
    @if($exp->responsibilities)
    <p style="margin-top:4px;color:#475569;">{{ $exp->responsibilities }}</p>
    @endif
  </div>
  @endforeach
  @endif

  @if($profile->educations->count())
  <h2>Education</h2>
  @foreach($profile->educations->sortByDesc('passing_year') as $edu)
  <div class="section-item">
    <div class="flex">
      <strong>{{ $edu->degree }} in {{ $edu->field_of_study }}</strong>
      <span class="meta">{{ $edu->passing_year }}</span>
    </div>
    <p class="meta">
      {{ $edu->institution_name }}
      @if($edu->result_value)
      &nbsp;&middot;&nbsp;{{ $edu->result_value }}
      @endif
    </p>
  </div>
  @endforeach
  @endif

  @if($profile->skills->count())
  <h2>Skills</h2>
  <div>
    @foreach($profile->skills as $skill)
    <span class="skill">{{ $skill->name }}</span>
    @endforeach
  </div>
  @endif

</div>
</body>
</html>