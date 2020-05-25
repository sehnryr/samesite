<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function start(Request $request)
    {
        $home = config('samesite.home');

        if ($request->getHost() != $home) {
            return redirect("https://{$home}/setup/start");
        }

        $type = $request->route('type');
        $test = $this->loadTest($request);
        $this->log($test, "Starting {$type} test");

        if ($this->isPost($request)) {
            return view('redirect', [
                'url' => $this->redirect('shared', "test/shared/{$type}", $request->isSecure(), $test),
                'post' => true,
            ]);
        }

        return view('tests', [
            'shared' => $this->redirect('shared', 'test/shared/'.$type, $request->isSecure(), $test),
            'external' => $this->redirect('external', 'test/external/'.$type, $request->isSecure(), $test),
            'redirect' => $this->redirect('shared', 'test/shared/'.$type, $request->isSecure(), $test),
        ]);
    }

    public function shared(Request $request)
    {
        $type = $request->route('type');
        $test = $this->loadTest($request);
        $test->appendCookies($request);
        $test->save();

        if ($this->isIframe($request)) {
            return view('iframe');
        }

        return view('redirect', [
            'url' => $this->redirect('external', "test/external/{$type}", $request->isSecure(), $test),
            'post' => $this->isPost($request),
        ]);
    }

    public function external(Request $request)
    {
        $type = $request->route('type');
        $test = $this->loadTest($request);
        $test->appendCookies($request);
        $test->save();

        if ($this->isIframe($request)) {
            return view('iframe');
        }

        // finshed initial GET round, redirect to POST
        if (! $this->isPost($request)) {
            return view('redirect', [
                'url' => $this->redirect('home', "test/start/{$type}", $request->isSecure(), $test),
                'post' => true,
            ]);
        }

        // redirect to insecure tests after POST round
        if ($request->isSecure()) {
            return view('redirect', ['url' => $this->redirect('home', "test/start/{$type}", false, $test)]);
        }

        // delay redirect for delayed tests after insecure round
        if ($this->isRecent($request)) {
            return view('redirect', ['url' => $this->redirect('home', 'test/start/delayed', true, $test), 'delay' => 120]);
        }

        // And we're finally done
        return view('redirect', ['url' => $this->redirect('home', 'results', true, $test)]);
    }

    public function results(Request $request)
    {
        return view('results', ['test' => $this->loadTest($request)]);
    }

    protected function isIframe(Request $request): bool
    {
        return $request->query('method') === 'iframe';
    }

    protected function isRecent(Request $request): bool
    {
        return $request->route('type') === 'recent';
    }

    protected function isPost(Request $request): bool
    {
        return $request->isMethod('POST');
    }
}
