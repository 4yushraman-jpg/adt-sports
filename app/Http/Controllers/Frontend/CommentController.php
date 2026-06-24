<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\CommenterIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;

class CommentController extends Controller
{
    /**
     * Accept a comment from an identified (subscribed) visitor. The author's
     * name + email come from the signed identity cookie — set when they
     * subscribed-to-comment — not from the form, so we never re-ask. Stored
     * unapproved; a honeypot + rate limit guard against bots.
     */
    public function store(Request $request, Article $article)
    {
        abort_unless($article->isPublished(), 404);

        // Must have subscribed first. If not, bounce back to the gate.
        $identity = CommenterIdentity::get($request);
        if ($identity === null) {
            return $this->redirect($article, 'subscribe');
        }

        // Honeypot: real users never fill this. Pretend success for bots.
        if ($request->filled('hp_url')) {
            return $this->redirect($article, 'pending');
        }

        // Validate manually: the article page is cached for guests, so a
        // redirect-back would serve the cached HTML *without* the @error output.
        // Instead we send the visitor to a distinct, cache-safe ?comment=error URL.
        $validator = Validator::make($request->all(), [
            'body' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return $this->redirect($article, 'error');
        }

        $body = $validator->validated()['body'];

        // Suppress exact duplicates (same article + email + body) — a cheap
        // brake on double-submits and replayed spam. Compare against the
        // sanitized form so it matches what the Comment mutator will store.
        $cleanBody = Purifier::clean($body, 'comment');
        $duplicate = $article->comments()
            ->where('author_email', $identity['email'])
            ->where('body', $cleanBody)
            ->exists();

        if (! $duplicate) {
            $article->comments()->create([
                'author_name'  => $identity['name'],
                'author_email' => $identity['email'],
                'body'         => $body,
                'approved'     => false,
                'ip'           => $request->ip(),
            ]);
        }

        return $this->redirect($article, 'pending');
    }

    /**
     * Redirect to a distinct URL (?comment=pending|error) rather than a flash,
     * so the outcome survives the guest full-page cache (separate cache key).
     */
    private function redirect(Article $article, string $status)
    {
        return redirect()->to(route('article', $article->slug) . '?comment=' . $status . '#comments');
    }
}
