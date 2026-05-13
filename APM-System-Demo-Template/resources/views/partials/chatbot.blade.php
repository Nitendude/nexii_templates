@php
    $user = Auth::user();
    $needsOnboarding = $user && ($user->must_change_password || !$user->profile_completed);
@endphp

@unless($needsOnboarding)
    <div class="chatbot-fab">
        <button class="btn btn-primary rounded-circle shadow" type="button" id="chatbotToggle" aria-label="Open chatbot">
            <i class="bi bi-robot"></i>
        </button>
    </div>

    <div class="chatbot-window shadow" id="chatbotWindow" aria-hidden="true">
        <div class="chatbot-header">
            <div class="fw-semibold">APM Assistant</div>
            <button type="button" class="btn btn-sm btn-light" id="chatbotClose" aria-label="Close chatbot">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message bot">
                Hi! I can help with questions about using the system.
            </div>
        </div>
        <form class="chatbot-input" id="chatbotForm">
            @csrf
            <input type="text" class="form-control" id="chatbotInput" placeholder="Type your question..." maxlength="1000" required>
            <button class="btn btn-primary" type="submit">Send</button>
        </form>
    </div>
@endunless
