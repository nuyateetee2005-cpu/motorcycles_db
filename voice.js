function speak(text) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();

        const speech = new SpeechSynthesisUtterance(text);

        speech.lang = 'th-TH';
        speech.rate = 0.9;
        speech.pitch = 1;

        window.speechSynthesis.speak(speech);
    } else {
        alert("เบราว์เซอร์นี้ไม่รองรับระบบเสียง");
    }
}


/* =========================================
   หยุดเสียงที่กำลังพูด
   ========================================= */

function stopSpeak() {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }
}


/* =========================================
   ตรวจสอบว่าระบบเสียงพร้อมใช้งานหรือไม่
   ========================================= */

function isSpeechSupported() {
    return 'speechSynthesis' in window;
}


/* =========================================
   พูดข้อความหลังจากรอสักครู่
   ใช้สำหรับระบบถาม-ตอบ
   ========================================= */

function speakAfter(text, delay = 300) {

    setTimeout(function () {

        if (isSpeechSupported()) {
            speak(text);
        } else {
            alert("เบราว์เซอร์นี้ไม่รองรับระบบเสียง");
        }

    }, delay);
}