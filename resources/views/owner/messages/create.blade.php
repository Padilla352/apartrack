@extends('owner.layouts.app')

@section('title', 'New Message')
@section('page-title', 'new message')

@section('content')
<div class="create-message-container">
    <div class="message-card">
        <div class="message-header">
            <div class="header-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="header-text">
                <h2>Start a Conversation</h2>
                <p>Send a message to a tenant</p>
            </div>
        </div>
        
        <form action="{{ route('owner.messages.store') }}" method="POST" class="message-form" id="messageForm">
            @csrf
            
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-user"></i>
                    <span>Recipient</span>
                </div>
                
                <div class="form-group">
                    <label for="tenant_id">Select Tenant <span class="required">*</span></label>
                    <div class="select-wrapper">
                        <i class="fas fa-users select-icon"></i>
                        <select name="tenant_id" id="tenant_id" class="form-control" required>
                            <option value="">-- Choose a tenant --</option>
                            @if(isset($tenants) && $tenants->count())
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">
                                        {{ $tenant->name }} 
                                        @if($tenant->email) ({{ $tenant->email }}) @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Your message will start a new conversation with the tenant.
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-pen"></i>
                    <span>Message</span>
                </div>
                
                <div class="form-group">
                    <label for="message">Message <span class="required">*</span></label>
                    <div class="textarea-wrapper">
                        <i class="fas fa-comment input-icon top-icon"></i>
                        <textarea name="message" id="message" class="form-control" rows="6" 
                                  placeholder="Write your message here..." required></textarea>
                    </div>
                    <div class="char-counter">
                        <i class="fas fa-keyboard"></i> <span id="charCount">0</span> characters
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="{{ route('owner.messages.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .create-message-container {
        max-width: 800px;
        margin: 0 auto;
    }
    .message-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .message-header {
        background: #f0f0f0;
        padding: 24px 28px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .header-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #007BFF 0%, #00A2FF 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .header-icon i {
        font-size: 28px;
        color: white;
    }
    .header-text h2 {
        font-size: 20px;
        font-weight: 700;
        color: #000333;
        margin: 0 0 4px 0;
    }
    .header-text p {
        font-size: 13px;
        color: #6B7280;
        margin: 0;
    }
    .message-form {
        padding: 28px;
    }
    .form-section {
        margin-bottom: 28px;
    }
    .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e0e0e0;
    }
    .section-title i {
        font-size: 18px;
        color: #007BFF;
    }
    .section-title span {
        font-size: 14px;
        font-weight: 600;
        color: #333333;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #333333;
        margin-bottom: 8px;
    }
    .required {
        color: #D90404;
    }
    .select-wrapper,
    .textarea-wrapper {
        position: relative;
    }
    .select-icon,
    .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 14px;
        pointer-events: none;
    }
    .top-icon {
        top: 16px;
        transform: none;
    }
    .form-control {
        width: 100%;
        padding: 12px 16px 12px 42px;
        border: 1px solid #E0E0E0;
        border-radius: 12px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
        background: white;
        color: #333333;
    }
    textarea.form-control {
        padding-top: 14px;
        resize: vertical;
        min-height: 150px;
    }
    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%23666' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 20px;
    }
    .form-control:focus {
        outline: none;
        border-color: #007BFF;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }
    .form-hint {
        margin-top: 8px;
        font-size: 11px;
        color: #6B7280;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .form-hint i {
        font-size: 12px;
        color: #007BFF;
    }
    .char-counter {
        margin-top: 8px;
        font-size: 11px;
        color: #6B7280;
        text-align: right;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }
    .char-counter i {
        font-size: 12px;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 16px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #E0E0E0;
    }
    .btn-cancel {
        background: #F0F0F0;
        color: #333333;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-cancel:hover {
        background: #E0E0E0;
        color: #000333;
    }
    .btn-send {
        background: linear-gradient(135deg, #007BFF 0%, #00A2FF 100%);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-send:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }
    @media (max-width: 640px) {
        .message-card {
            border-radius: 16px;
        }
        .message-header {
            padding: 20px;
        }
        .message-form {
            padding: 20px;
        }
        .form-row {
            flex-direction: column;
            gap: 0;
        }
        .form-actions {
            flex-direction: column-reverse;
        }
        .btn-cancel,
        .btn-send {
            width: 100%;
            justify-content: center;
        }
        .header-icon {
            width: 48px;
            height: 48px;
        }
        .header-icon i {
            font-size: 24px;
        }
        .header-text h2 {
            font-size: 18px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const msg = document.getElementById('message');
        const counter = document.getElementById('charCount');
        if (msg && counter) {
            msg.addEventListener('input', function() {
                counter.textContent = this.value.length;
            });
        }
        
        const form = document.getElementById('messageForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const tenant = document.getElementById('tenant_id').value;
                const message = document.getElementById('message').value.trim();
                if (!tenant) {
                    e.preventDefault();
                    alert('Please select a tenant.');
                    return false;
                }
                if (!message) {
                    e.preventDefault();
                    alert('Please enter a message.');
                    return false;
                }
                if (message.length < 3) {
                    e.preventDefault();
                    alert('Message must be at least 3 characters.');
                    return false;
                }
            });
        }
    });
</script>
@endsection