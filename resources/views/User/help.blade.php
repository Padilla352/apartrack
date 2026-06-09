@extends('layouts.app')

@section('title', 'Help Center - APARTrack')

@section('styles')
<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #1e40af;
        --primary-light: #eff6ff;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-600: #475569;
        --gray-800: #1e293b;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    * {
        font-family: 'Poppins', sans-serif;
    }

    .help-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    /* Header */
    .help-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .help-header .icon {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }
    .help-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 0.5rem;
    }
    .help-header p {
        color: var(--gray-600);
        font-size: 0.9rem;
    }

    /* FAQ Section */
    .faq-section {
        background: var(--white);
        border-radius: 1.25rem;
        border: 1px solid var(--gray-200);
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
    }
    .faq-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .faq-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-800);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .faq-header h2 i {
        color: var(--primary);
    }
    .faq-search {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 2rem;
        font-size: 0.85rem;
        width: 220px;
        outline: none;
        transition: all 0.2s;
    }
    .faq-search:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
    }

    /* FAQ Accordion Items */
    .faq-item {
        border-bottom: 1px solid var(--gray-100);
        padding: 0.75rem 0;
    }
    .faq-item:last-child {
        border-bottom: none;
    }
    .faq-question {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        padding: 0.5rem 0;
        font-weight: 500;
        color: var(--gray-800);
    }
    .faq-question span {
        font-size: 0.95rem;
    }
    .faq-question i {
        color: var(--primary);
        transition: transform 0.2s;
        font-size: 0.8rem;
    }
    .faq-question.active i {
        transform: rotate(180deg);
    }
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        color: var(--gray-600);
        font-size: 0.85rem;
        line-height: 1.5;
    }
    .faq-answer.open {
        max-height: 300px;
        padding-top: 0.25rem;
        padding-bottom: 0.5rem;
    }
    .no-results {
        text-align: center;
        padding: 2rem;
        color: var(--gray-500);
    }
    .hidden {
        display: none;
    }

    /* Feedback Card */
    .feedback-card {
        background: var(--white);
        border-radius: 1.25rem;
        border: 1px solid var(--gray-200);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
    }
    .feedback-card h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .feedback-card h2 i {
        color: var(--primary);
    }
    .feedback-card p {
        color: var(--gray-600);
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
    }

    /* Form Styles */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-group.full-width {
        grid-column: span 2;
    }
    label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.25rem;
    }
    .form-control {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border: 1px solid var(--gray-200);
        border-radius: 0.75rem;
        font-size: 0.9rem;
        outline: none;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
    }
    textarea.form-control {
        resize: vertical;
    }
    .error-message {
        color: #ef4444;
        font-size: 0.7rem;
        margin-top: 0.25rem;
        display: none;
    }
    .char-counter {
        text-align: right;
        font-size: 0.7rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
    }
    .submit-btn {
        background: linear-gradient(100deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 0.7rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        width: 100%;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        filter: brightness(1.05);
    }
    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .spinner {
        width: 1rem;
        height: 1rem;
        border: 2px solid white;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        display: none;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Contact Banner */
    .contact-banner {
        background: var(--primary-light);
        border-radius: 1rem;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .contact-banner span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-dark);
        font-size: 0.85rem;
    }
    .contact-banner i {
        font-size: 1rem;
    }

    /* Toast */
    .toast {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        background: var(--white);
        border-left: 4px solid #10b981;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        box-shadow: var(--shadow-lg);
        z-index: 1100;
        display: none;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: var(--gray-800);
    }
    .toast.error {
        border-left-color: #ef4444;
    }
    .toast button {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        color: var(--gray-500);
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
        .form-group.full-width {
            grid-column: span 1;
        }
        .faq-header {
            flex-direction: column;
            align-items: stretch;
        }
        .faq-search {
            width: 100%;
        }
        .contact-banner {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
<div class="help-container">
    <div class="help-header">
        <div class="icon"><i class="fas fa-life-ring"></i></div>
        <h1>How can we help you?</h1>
        <p>Find answers to common questions or send us a message.</p>
    </div>

    <!-- Contact Banner -->
    <div class="contact-banner">
        <span><i class="fas fa-envelope"></i> support@apartrack.com</span>
        <span><i class="fas fa-phone-alt"></i> +63 912 3456 789</span>
        <span><i class="fas fa-clock"></i> Mon-Fri, 9AM - 6PM</span>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <div class="faq-header">
            <h2><i class="fas fa-question-circle"></i> Frequently Asked Questions</h2>
            <input type="text" id="faqSearch" placeholder="Search FAQs..." class="faq-search">
        </div>
        <div id="faqList">
            @foreach($faqs as $faq)
            <div class="faq-item" data-question="{{ e($faq->question) }}" data-answer="{{ e($faq->answer) }}">
                <div class="faq-question">
                    <span>{{ $faq->question }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">{{ $faq->answer }}</div>
            </div>
            @endforeach
        </div>
        <div id="noResults" class="no-results hidden">No FAQs match your search.</div>
    </div>

    <!-- Feedback Form -->
    <div class="feedback-card">
        <h2><i class="fas fa-paper-plane"></i> Send us a message</h2>
        <p>Have a question or suggestion? Fill out the form below and we'll get back to you soon.</p>
        <form id="feedbackForm">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                    <div class="error-message" id="nameError"></div>
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <div class="error-message" id="emailError"></div>
                </div>
            </div>
            <div class="form-group">
                <label>Issue Type *</label>
                <select id="issue_type" name="issue_type" class="form-control" required>
                    <option value="">Select an option</option>
                    <option value="general">General Inquiry</option>
                    <option value="technical">Technical Issue</option>
                    <option value="billing">Billing Question</option>
                    <option value="feature">Feature Request</option>
                </select>
                <div class="error-message" id="issueTypeError"></div>
            </div>
            <div class="form-group">
                <label>Message *</label>
                <textarea id="message" name="message" rows="4" class="form-control" required maxlength="1000"></textarea>
                <div class="char-counter"><span id="charCount">0</span>/1000</div>
                <div class="error-message" id="messageError"></div>
            </div>
            <button type="submit" id="submitBtn" class="submit-btn">
                <span>Submit Feedback</span>
                <div id="loadingSpinner" class="spinner"></div>
            </button>
        </form>
    </div>
</div>

<div id="toast" class="toast">
    <span id="toastMsg"></span>
    <button onclick="this.parentElement.style.display='none'">×</button>
</div>
@endsection

@section('scripts')
<script>
    // ========== FAQ ACCORDION ==========
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const isActive = btn.classList.contains('active');
            // Close all others
            document.querySelectorAll('.faq-question').forEach(q => {
                q.classList.remove('active');
                q.nextElementSibling.classList.remove('open');
            });
            if (!isActive) {
                btn.classList.add('active');
                btn.nextElementSibling.classList.add('open');
            }
        });
    });

    // ========== SEARCH ==========
    const searchInput = document.getElementById('faqSearch');
    const faqItems = document.querySelectorAll('.faq-item');
    const noResultsDiv = document.getElementById('noResults');
    let debounceTimer;

    function filterFaqs() {
        const term = searchInput.value.toLowerCase().trim();
        let anyVisible = false;
        faqItems.forEach(item => {
            const question = item.getAttribute('data-question').toLowerCase();
            const answer = item.getAttribute('data-answer').toLowerCase();
            const matches = question.includes(term) || answer.includes(term);
            item.style.display = matches ? 'block' : 'none';
            if (matches) anyVisible = true;
            if (!matches && item.querySelector('.faq-answer.open')) {
                const qBtn = item.querySelector('.faq-question');
                qBtn.classList.remove('active');
                item.querySelector('.faq-answer').classList.remove('open');
            }
        });
        noResultsDiv.classList.toggle('hidden', anyVisible);
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterFaqs, 300);
    });

    // ========== FEEDBACK FORM ==========
    const form = document.getElementById('feedbackForm');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('loadingSpinner');
    const charCountSpan = document.getElementById('charCount');
    const messageField = document.getElementById('message');
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');

    messageField.addEventListener('input', function() {
        const len = this.value.length;
        charCountSpan.innerText = len;
        if (len > 950) charCountSpan.style.color = '#eab308';
        else charCountSpan.style.color = '#475569';
        if (len >= 1000) charCountSpan.style.color = '#ef4444';
    });

    const fields = ['name', 'email', 'issue_type', 'message'];
    fields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.addEventListener('input', () => {
                const errDiv = document.getElementById(field + 'Error');
                if (errDiv) {
                    errDiv.style.display = 'none';
                    errDiv.innerText = '';
                }
            });
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (submitBtn.disabled) return;
        submitBtn.disabled = true;
        spinner.style.display = 'block';

        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
            el.innerText = '';
        });

        const formData = new FormData(form);
        try {
            const response = await fetch('{{ route("help.feedback") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await response.json();
            if (response.ok && data.success) {
                showToast(data.message, 'success');
                form.reset();
                charCountSpan.innerText = '0';
            } else if (response.status === 422 && data.errors) {
                for (let [field, errors] of Object.entries(data.errors)) {
                    const errorDiv = document.getElementById(field + 'Error');
                    if (errorDiv) {
                        errorDiv.innerText = errors[0];
                        errorDiv.style.display = 'block';
                    }
                }
                showToast('Please fix the errors above.', 'error');
            } else {
                showToast(data.message || 'Something went wrong. Please try again.', 'error');
            }
        } catch (err) {
            showToast('Network error. Please check your connection.', 'error');
        } finally {
            submitBtn.disabled = false;
            spinner.style.display = 'none';
        }
    });

    function showToast(message, type = 'success') {
        toastMsg.innerText = message;
        toast.classList.remove('error');
        if (type === 'error') toast.classList.add('error');
        toast.style.display = 'flex';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 5000);
    }
</script>
@endsection