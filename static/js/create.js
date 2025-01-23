document.addEventListener('DOMContentLoaded', function () {
    const yoyakuRadio = document.querySelector('input[name="modified"][value="yoyaku"]');
    const nowRadio = document.querySelector('input[name="modified"][value="now"]');
    const timePicker = document.getElementById('scheduledTime');

    console.log('DOM Loaded:', { yoyakuRadio, nowRadio, timePicker });

    function toggleTimePicker() {
        if (yoyakuRadio.checked) {
            timePicker.closest('.form-group').style.display = 'block'; 
        } else {
            timePicker.closest('.form-group').style.display = 'none';
        }
    }

    toggleTimePicker();
});

document.addEventListener('DOMContentLoaded', function () {
    const turnstileDiv = document.querySelector(".cf-turnstile");
    if (turnstileDiv) {
        turnstile.render(turnstileDiv, {
            sitekey: "0x4AAAAAAA5RjmcHEOkVY3BE",
            theme: "light"
        });
    }
});

if (!window.turnstile) {
    const script = document.createElement("script");
    script.src = "https://challenges.cloudflare.com/turnstile/v0/api.js";
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}
const turnstileDiv = document.querySelector(".cf-turnstile");
if (turnstileDiv && !turnstileDiv.hasAttribute("data-rendered")) {
    turnstile.render(turnstileDiv, {
        sitekey: "0x4AAAAAAA5RjmcHEOkVY3BE",
        theme: "light",
    });
    turnstileDiv.setAttribute("data-rendered", "true");
}
document.addEventListener('DOMContentLoaded', function () {
    const yoyakuRadio = document.querySelector('input[name="modified"][value="yoyaku"]');
    const nowRadio = document.querySelector('input[name="modified"][value="now"]');
    const timePicker = document.getElementById('timePicker');

    console.log('DOM Loaded:', { yoyakuRadio, nowRadio, timePicker });

    function toggleTimePicker() {
        if (yoyakuRadio.checked) {
            timePicker.style.display = 'block';
        } else {
            timePicker.style.display = 'none';
        }
    }

    yoyakuRadio.addEventListener('change', toggleTimePicker);
    nowRadio.addEventListener('change', toggleTimePicker);

    toggleTimePicker();
});
document.addEventListener('DOMContentLoaded', function () {
    const timePicker = document.getElementById('scheduledTime');

    function setMinDateTime() {
        const now = new Date();
        const pad = (n) => (n < 10 ? '0' + n : n);
        const currentDateTime = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
        timePicker.min = currentDateTime; 
        console.log('Min DateTime Set:', currentDateTime); 
    }

    setMinDateTime();
});
