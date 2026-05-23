function switchTab(tab) {
  document.getElementById('form-login').classList.toggle('d-none', tab !== 'login');
  document.getElementById('form-signup').classList.toggle('d-none', tab !== 'signup');
  document.getElementById('tab-login').classList.toggle('active', tab === 'login');
  document.getElementById('tab-signup').classList.toggle('active', tab === 'signup');
}

function togglePwd(fieldId, btn) {
  const field = document.getElementById(fieldId);
  const icon = btn.querySelector('i');
  if (field.type === 'password') {
    field.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    field.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}

// Client-side confirm-password match hint
const pwSignup = document.getElementById('pw-signup');
const pwConfirm = document.getElementById('pw-confirm');
if (pwConfirm) {
  pwConfirm.addEventListener('input', function () {
    if (pwSignup && this.value && this.value !== pwSignup.value) {
      this.style.borderColor = 'rgba(239,68,68,.5)';
    } else {
      this.style.borderColor = '';
    }
  });
}