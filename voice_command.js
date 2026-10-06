// ========================================
// ระบบคำสั่งเสียงสำหรับระบบเช่ารถมอเตอร์ไซค์
// รองรับ:
// Click / PCX / Giorno
// ตรวจรถว่าง / ราคา / จำนวนรถ
// การเช่า / รายการเช่า / วันคืนรถ / สถานะ
// กลับหน้าหลัก / ออกจากระบบ
// ========================================


// ========================================
// จำรุ่นรถล่าสุดที่ผู้ใช้ถาม
// ใช้สำหรับคำถามต่อเนื่อง เช่น
// "PCX ว่างไหม"
// แล้วถามต่อว่า "แล้วราคาเท่าไหร่"
// ========================================
let lastVoiceModel = sessionStorage.getItem("lastVoiceModel") || "";


// ========================================
// แปลงข้อความให้เป็นรูปแบบที่ตรวจสอบง่าย
// ========================================
function normalizeVoiceText(text) {

    return text
        .toLowerCase()
        .trim()
        .replace(/\s+/g, " ");

}


// ========================================
// ตรวจสอบว่าคำพูดหมายถึงรุ่นรถอะไร
// Click / PCX / Giorno
// ========================================
function detectMotorcycleModel(text) {

    const value = normalizeVoiceText(text);


    // ==============================
    // PCX
    // ==============================
    if (
        value.includes("pcx") ||
        value.includes("พีซีเอ็กซ์") ||
        value.includes("พีซีเอ็ก")
    ) {
        return "PCX";
    }


    // ==============================
    // Click
    // ==============================
    if (
        value.includes("click") ||
        value.includes("คลิก") ||
        value.includes("คลิ๊ก")
    ) {
        return "Click";
    }


    // ==============================
    // Giorno
    // ==============================
    if (
        value.includes("giorno") ||
        value.includes("จีออโน่") ||
        value.includes("จีออโน") ||
        value.includes("จีออโน่")
    ) {
        return "Giorno";
    }


    return "";
}


// ========================================
// ตรวจสอบจำนวนรถว่างจากฐานข้อมูล
// ========================================
function checkAvailableMotorcycles() {

    fetch("voice_query.php")
        .then(response => response.json())
        .then(data => {

            if (!data.success) {

                speak("ขออภัย ไม่สามารถตรวจสอบข้อมูลรถได้ค่ะ");
                return;
            }

            let message = "";


            // =========================
            // ไม่มีรถว่าง
            // =========================
            if (data.total === 0) {

                message =
                    "ตอนนี้ไม่มีรถมอเตอร์ไซค์ว่างค่ะ";

            }


            // =========================
            // มีรถว่าง
            // =========================
            else {

                message =
                    "ตอนนี้มีรถมอเตอร์ไซค์ว่างทั้งหมด " +
                    data.total +
                    " คันค่ะ ";


                // แสดงจำนวนรถแต่ละรุ่น
                if (data.models && data.models.length > 0) {

                    data.models.forEach(function(item) {

                        message +=
                            "รุ่น " +
                            item.model +
                            " ว่าง " +
                            item.total +
                            " คันค่ะ ";

                    });

                }

            }


            // แสดงใน Console
            console.log("คำตอบ:", message);


            // ให้ระบบพูด
            speak(message);


            // แสดงข้อความบนหน้าจอ
            alert(message);

        })
        .catch(error => {

            console.error(
                "Voice Query Error:",
                error
            );

            speak(
                "ขออภัย ไม่สามารถเชื่อมต่อข้อมูลรถได้ค่ะ"
            );

        });
}


// ========================================
// ตรวจสอบรถตามรุ่น
// เช่น
// PCX ว่างไหม
// Click ว่างกี่คัน
// Giorno ราคาเท่าไหร่
// ========================================
function checkMotorcycleModel(model, originalText) {

    lastVoiceModel = model;

    sessionStorage.setItem(
        "lastVoiceModel",
        model
    );


    // ========================================
    // ส่งข้อความจริงไปยัง PHP
    // ========================================
    const url =
        "voice_query.php?model=" +
        encodeURIComponent(model) +
        "&query=" +
        encodeURIComponent(originalText);


    fetch(url)
        .then(response => response.json())
        .then(data => {

            if (!data.success) {

                speak(
                    "ขออภัย ไม่สามารถตรวจสอบข้อมูลรถได้ค่ะ"
                );

                return;
            }


            let message = "";


            // ========================================
            // ตรวจสอบว่ามีข้อมูลรุ่นนี้หรือไม่
            // ========================================
            if (
                data.model_found === false ||
                data.model_found === 0
            ) {

                message =
                    "ไม่พบข้อมูลรถรุ่น " +
                    model +
                    " ในระบบค่ะ";

            }


            // ========================================
            // ถามเรื่องราคา
            // ========================================
            else if (
                originalText.includes("ราคา") ||
                originalText.includes("เท่าไหร่") ||
                originalText.includes("กี่บาท") ||
                originalText.includes("บาทต่อวัน") ||
                originalText.includes("ค่าเช่า")
            ) {

                if (
                    data.model_price !== undefined &&
                    data.model_price !== null
                ) {

                    message =
                        "รถรุ่น " +
                        model +
                        " ราคา " +
                        Number(data.model_price).toLocaleString() +
                        " บาทต่อวันค่ะ";

                } else {

                    message =
                        "ขออภัย ไม่พบข้อมูลราคาของรถรุ่น " +
                        model +
                        " ค่ะ";

                }

            }


            // ========================================
            // ถามว่ามีรถรุ่นนี้ไหม / ว่างไหม
            // ========================================
            else {

                const available =
                    Number(data.model_available || 0);


                if (available > 0) {

                    message =
                        "ตอนนี้มีรถรุ่น " +
                        model +
                        " ว่าง " +
                        available +
                        " คันค่ะ";

                } else {

                    message =
                        "ตอนนี้รถรุ่น " +
                        model +
                        " ไม่มีรถว่างค่ะ";

                }

            }


            console.log(
                "คำตอบรุ่นรถ:",
                message
            );


            speak(message);

            alert(message);

        })
        .catch(error => {

            console.error(
                "Model Query Error:",
                error
            );

            speak(
                "ขออภัย ไม่สามารถตรวจสอบข้อมูลรถรุ่นนี้ได้ค่ะ"
            );

        });
}


// ========================================
// ตรวจสอบรถตามราคาสูงสุด
// เช่น
// "มีรถราคาไม่เกิน 200 บาทไหม"
// ========================================
function checkMotorcyclesByPrice(maxPrice) {

    const url =
        "voice_query.php?max_price=" +
        encodeURIComponent(maxPrice);


    fetch(url)
        .then(response => response.json())
        .then(data => {

            if (!data.success) {

                speak(
                    "ขออภัย ไม่สามารถตรวจสอบราคาเช่ารถได้ค่ะ"
                );

                return;
            }


            let message = "";


            if (
                !data.price_motorcycles ||
                data.price_motorcycles.length === 0
            ) {

                message =
                    "ตอนนี้ไม่มีรถที่ราคาไม่เกิน " +
                    Number(maxPrice).toLocaleString() +
                    " บาทต่อวันค่ะ";

            }

            else {

                message =
                    "มีรถที่ราคาไม่เกิน " +
                    Number(maxPrice).toLocaleString() +
                    " บาทต่อวันค่ะ ";


                data.price_motorcycles.forEach(
                    function(item) {

                        message +=
                            "รุ่น " +
                            item.model +
                            " ราคา " +
                            Number(item.price_per_day)
                                .toLocaleString() +
                            " บาทต่อวันค่ะ ";

                    }
                );

            }


            console.log(
                "คำตอบค้นหาตามราคา:",
                message
            );


            speak(message);

            alert(message);

        })
        .catch(error => {

            console.error(
                "Price Query Error:",
                error
            );

            speak(
                "ขออภัย ไม่สามารถตรวจสอบราคารถได้ค่ะ"
            );

        });
}


// ========================================
// เปิดหน้ารายการรถ
// ========================================
function openMotorcycleList() {

    speak(
        "กำลังเปิดรายการรถมอเตอร์ไซค์ค่ะ"
    );


    setTimeout(function() {

        window.location.href =
            "motorcycles_customer.php";

    }, 1200);

}


// ========================================
// เปิดหน้าการเช่ารถ
// ========================================
function openRentalPage() {

    speak(
        "กำลังเปิดหน้าเช่ามอเตอร์ไซค์ค่ะ"
    );


    setTimeout(function() {

        window.location.href =
            "rentals_customer.php";

    }, 1200);

}


// ========================================
// เปิดหน้าสรุปการเช่า
// ========================================
function openRentalSummary() {

    speak(
        "กำลังเปิดรายการเช่าของคุณค่ะ"
    );


    setTimeout(function() {

        window.location.href =
            "rentals_summary.php";

    }, 1200);

}


// ========================================
// เปิด Dashboard
// ========================================
function openDashboard() {

    speak(
        "กำลังกลับหน้าหลักค่ะ"
    );


    setTimeout(function() {

        window.location.href =
            "dashboard_customer.php";

    }, 1200);

}


// ========================================
// ออกจากระบบ
// ========================================
function logoutVoice() {

    speak(
        "กำลังออกจากระบบค่ะ"
    );


    setTimeout(function() {

        window.location.href =
            "logout.php";

    }, 1200);

}


// ========================================
// ระบบรับคำสั่งเสียง
// ========================================
function startVoiceCommand() {

    const SpeechRecognition =
        window.SpeechRecognition ||
        window.webkitSpeechRecognition;


    // ========================================
    // ตรวจสอบว่าเบราว์เซอร์รองรับหรือไม่
    // ========================================
    if (!SpeechRecognition) {

        alert(
            "เบราว์เซอร์นี้ไม่รองรับการสั่งงานด้วยเสียง"
        );

        return;
    }


    const recognition =
        new SpeechRecognition();


    // ========================================
    // ตั้งค่าภาษาไทย
    // ========================================
    recognition.lang = "th-TH";


    // ========================================
    // จับเสียงครั้งเดียว
    // ========================================
    recognition.continuous = false;


    // ========================================
    // ไม่แสดงผลระหว่างพูด
    // ========================================
    recognition.interimResults = false;


    // ========================================
    // ให้ระบบพูดก่อนเริ่มฟัง
    // ========================================
    speak(
        "กรุณาพูดคำสั่ง"
    );


    setTimeout(function() {

        try {

            recognition.start();

        } catch (error) {

            console.log(
                "Recognition start error:",
                error
            );

        }

    }, 500);


    // ========================================
    // เมื่อระบบจับเสียงได้
    // ========================================
    recognition.onresult = function(event) {

        const text =
            normalizeVoiceText(
                event.results[0][0].transcript
            );


        console.log(
            "คำที่ระบบจับได้:",
            text
        );


        // ========================================
        // ตรวจสอบชื่อรุ่นรถ
        // ========================================
        const model =
            detectMotorcycleModel(text);


        // ========================================
        // 🚗 ตรวจสอบคำถามเกี่ยวกับรุ่นรถ
        // ต้องอยู่ก่อนคำสั่ง "รถว่าง"
        // ========================================
        if (model !== "") {

            console.log(
                "ตรวจพบรุ่นรถ:",
                model
            );


            // ========================================
            // ถามราคา
            // เช่น
            // PCX ราคาเท่าไหร่
            // Click กี่บาท
            // Giorno ค่าเช่าเท่าไหร่
            // ========================================
            if (
                text.includes("ราคา") ||
                text.includes("เท่าไหร่") ||
                text.includes("กี่บาท") ||
                text.includes("บาทต่อวัน") ||
                text.includes("ค่าเช่า")
            ) {

                speak(
                    "กำลังตรวจสอบราคารถรุ่น " +
                    model +
                    " ค่ะ"
                );


                setTimeout(function() {

                    checkMotorcycleModel(
                        model,
                        text
                    );

                }, 800);


                return;
            }


            // ========================================
            // ถามว่างไหม / ว่างกี่คัน
            // ========================================
            if (
                text.includes("ว่าง") ||
                text.includes("มีไหม") ||
                text.includes("มีหรือเปล่า") ||
                text.includes("หรือเปล่า") ||
                text.includes("เหลือไหม") ||
                text.includes("เหลือกี่คัน") ||
                text.includes("กี่คัน") ||
                text.includes("ให้เช่าไหม")
            ) {

                speak(
                    "กำลังตรวจสอบรถรุ่น " +
                    model +
                    " ค่ะ"
                );


                setTimeout(function() {

                    checkMotorcycleModel(
                        model,
                        text
                    );

                }, 800);


                return;
            }

        }


        // ========================================
        // 💰 ค้นหารถตามงบประมาณ
        // เช่น
        // มีรถราคาไม่เกิน 200 บาทไหม
        // รถไม่เกิน 300 บาท
        // ========================================
        const priceMatch =
            text.match(
                /(?:ไม่เกิน|ต่ำกว่า|ไม่ถึง|งบไม่เกิน|ราคาไม่เกิน)\s*(\d+)\s*(?:บาท)?/
            );


        if (priceMatch) {

            const maxPrice =
                parseInt(
                    priceMatch[1]
                );


            if (!isNaN(maxPrice)) {

                speak(
                    "กำลังค้นหารถที่ราคาไม่เกิน " +
                    maxPrice +
                    " บาทต่อวันค่ะ"
                );


                setTimeout(function() {

                    checkMotorcyclesByPrice(
                        maxPrice
                    );

                }, 800);


                return;
            }

        }


        // ========================================
        // 🚗 ตรวจสอบจำนวนรถว่าง
        // ต้องอยู่ก่อนคำสั่งรายการรถ
        // ========================================
        if (
            text.includes("รถว่างกี่คัน") ||
            text.includes("รถว่างกี่") ||
            text.includes("มีรถว่างกี่คัน") ||
            text.includes("รถมีว่างกี่คัน") ||
            text.includes("ตอนนี้รถว่างกี่คัน") ||
            text.includes("ตอนนี้มีรถว่างกี่คัน") ||
            text === "รถว่าง" ||
            text.includes("รถมีว่างไหม") ||
            text.includes("มีรถว่างไหม") ||
            text.includes("มีรถว่างหรือเปล่า") ||
            text.includes("ตอนนี้มีรถว่างไหม")
        ) {

            speak(
                "กำลังตรวจสอบจำนวนรถที่ว่างค่ะ"
            );


            setTimeout(function() {

                checkAvailableMotorcycles();

            }, 800);


            return;
        }


        // ========================================
        // 🏍️ รายการรถมอเตอร์ไซค์
        // ========================================
        if (
            text.includes("รายการรถ") ||
            text.includes("ดูรถ") ||
            text.includes("รถมอเตอร์ไซค์") ||
            text.includes("ดูมอเตอร์ไซค์") ||
            text.includes("มีรถอะไรบ้าง") ||
            text.includes("มีรถรุ่นอะไรบ้าง") ||
            text.includes("ดูรายการมอเตอร์ไซค์") ||
            text.includes("ขอดูรถ") ||
            text.includes("ขอดูรายการรถ") ||
            text.includes("เปิดรายการรถ")
        ) {

            openMotorcycleList();

            return;
        }


        // ========================================
        // 📋 เช่ารถ
        // ========================================
        if (
            text.includes("เช่ารถ") ||
            text.includes("เช่ามอเตอร์ไซค์") ||
            text.includes("จองรถ") ||
            text.includes("จองมอเตอร์ไซค์") ||
            text.includes("ต้องการเช่ารถ") ||
            text.includes("ฉันต้องการเช่ารถ") ||
            text.includes("ต้องการเช่ามอเตอร์ไซค์") ||
            text.includes("อยากเช่ารถ") ||
            text.includes("อยากเช่ามอเตอร์ไซค์") ||
            text.includes("จะเช่ารถ")
        ) {

            openRentalPage();

            return;
        }


        // ========================================
        // 🧾 สรุป / ประวัติการเช่า
        // ========================================
        if (
            text.includes("สรุปการเช่า") ||
            text.includes("รายการเช่า") ||
            text.includes("ประวัติการเช่า") ||
            text.includes("ดูประวัติ") ||
            text === "ประวัติ" ||
            text.includes("ดูรายการเช่าของฉัน") ||
            text.includes("รายการเช่าของฉัน") ||
            text.includes("ประวัติการเช่าของฉัน") ||
            text.includes("สรุปค่าเช่า")
        ) {

            openRentalSummary();

            return;
        }


        // ========================================
        // 🏍️ รถที่กำลังเช่าอยู่
        // ========================================
        if (
            text.includes("รถที่ฉันเช่าอยู่") ||
            text.includes("รถที่เช่าอยู่") ||
            text.includes("รถที่กำลังเช่า") ||
            text.includes("ตอนนี้ฉันเช่ารถคันไหนอยู่") ||
            text.includes("ฉันเช่ารถคันไหนอยู่") ||
            text.includes("ตอนนี้เช่ารถคันไหน") ||
            text.includes("รถที่ฉันกำลังเช่า")
        ) {

            speak(
                "กำลังเปิดข้อมูลการเช่ารถของคุณค่ะ"
            );


            setTimeout(function() {

                window.location.href =
                    "rentals_customer.php";

            }, 1200);


            return;
        }


        // ========================================
        // 📅 วันคืนรถ
        // ========================================
        if (
            text.includes("ต้องคืนรถวันไหน") ||
            text.includes("คืนรถวันไหน") ||
            text.includes("วันคืนรถ") ||
            text.includes("กำหนดคืนรถ") ||
            text.includes("รถต้องคืนวันไหน") ||
            text.includes("คืนรถเมื่อไหร่") ||
            text.includes("ต้องคืนเมื่อไหร่")
        ) {

            speak(
                "กำลังเปิดข้อมูลการเช่าของคุณเพื่อดูวัน