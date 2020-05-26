@extends('layout')

@section('content')

    <p>SameSite Cookie Status: <strong><code>&lt;iframe&gt;</code></strong></p>

    <iframe width=200 height=120 src='https://{{ $domains['home'] }}/cookies/read?test=iframe'></iframe>

    <p>Run another test from the <a href='https://{{ $domains['external'] }}/cookies/external'>External Site</a>.</p>

    <hr>

    <p><em>
        Since the page request within the <code>&lt;iframe&gt;</code> is a cross-site request,
        your browser will have checked the SameSite cookie attribute and only sent cookies that are allowed for requests
        within an <code>&lt;iframe&gt;</code>. All other cookies will have been blocked.
    </em></p>

    <p><em>
        <strong>"StrictCookie"</strong> (<code>SameSite=Strict</code>) should never sent on cross-site requests.<br>
        <strong>"LaxCookie"</strong> (<code>SameSite=Lax</code>) should only be sent on cross-site <code>GET</code> requests.<br>
        <strong>"SecureNoneCookie"</strong> (<code>SameSite=None; Secure</code>) should always be sent on cross-site requests.<br>
        <strong>"NoneCookie"</strong> (<code>SameSite=None</code>) is invalid and should never rejected by the browser (and never sent).
    </em></p>

@endsection
