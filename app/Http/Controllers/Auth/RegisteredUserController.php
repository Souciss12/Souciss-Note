<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Note;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $README = new Note();
        $README->title = 'Welcome to Souciss Note';
        $README->content = "# Welcome to Souciss Note !\n\n## How to use :\n- First press the **eye** or the **side by side** button right on top of the note title to see the Markdown preview.\n\t- If you press the **side by side** button please reopen the note to see the result.\n\n### Creating folders or notes\n- **Create note**: Click the \" + \" button or use the shortcut `Ctrl+Shift+M`\n- **Create folder**: Click the \" + \" button or use the shortcut `Ctrl+Shift+N`\n- **Rename note/folder**: Click on the note/folder and use the shortcut `F2`\n- **Delete note/folder**: Click on the note/folder and use the shotcut `Delete`\n- **Move note/folder**: Drag and drop notes/folders to move them where you want\n- **Save**: Everything is saved automatically\n\n### Formatting (Markdown)\n- **Headings**: `# Title1 / ## Title 2 / etc`\n- **Bold text**: `**text**`\n- **Italic**: `*text*`\n- **Lists**: `- item / 1. item`\n- So much more by clicking the `?` button on top of the note title\n\n### Search\n- Open search bar by clicking or use the shortcut `Ctrl+R`\n- Type text in the search bar\n- Use arrows the move in to te search results\n- Search works in both titles and content\n\n### Customization\n- Access settings to modify colors\n\nWe hope SoucissNote will be useful for organizing your ideas!";
        $README->user_id = $user->id;
        $README->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('note.index', absolute: false));
    }
}
