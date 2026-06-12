/**
 * Apex LMS - Client Side Security & UI Manipulation Engine
 * Handles structural state evaluations, interactive forms, and visibility overrides
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // 1. Interactive Tab Switching Logic (DOM Manipulation Ref: Fig 2.2 Toggle)
    const tabs = document.querySelectorAll(".tab-btn");
    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            tabs.forEach(t => t.classList.remove("active-state"));
            this.classList.add("active-state");
            console.log(`System Authorization Context Switched to: ${this.textContent.trim()}`);
        });
    });

    // 2. Gateway Login Validation Interceptor Matrix
    const loginForm = document.getElementById("portalLoginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function(event) {
            const cardId = document.getElementById("libraryCardId").value.trim();
            const password = document.getElementById("userPassword").value;
            
            // Comprehensive Input Validation System Error Triggers
            if (cardId === "" || password === "") {
                event.preventDefault();
                alert("Security Exception: Access credentials fields cannot contain null strings!");
                return false;
            }
            
            if (cardId.length < 5) {
                event.preventDefault();
                alert("Security Exception: System Identifiers must exceed 5 characters.");
                return false;
            }
        });
    }

    // 3. Live Real-Time Password Strength Structural Analyzer
    const passwordInput = document.getElementById("userPassword");
    const strengthIndicator = document.getElementById("strengthMeterText");

    if (passwordInput && strengthIndicator) {
        passwordInput.addEventListener("input", function() {
            const verificationValue = this.value;
            let trackingScore = 0;

            if (verificationValue.length >= 8) trackingScore++;
            if (/[A-Z]/.test(verificationValue)) trackingScore++;
            if (/[0-9]/.test(verificationValue)) trackingScore++;
            if (/[^A-Za-z0-9]/.test(verificationValue)) trackingScore++;

            // Live Text Preview and DOM Mutator Stream Processing
            if (verificationValue.length === 0) {
                strengthIndicator.textContent = "";
            } else if (trackingScore <= 2) {
                strengthIndicator.textContent = "Strength Profile: Weak ⚠️";
                strengthIndicator.style.color = "#FF3333";
            } else if (trackingScore === 3) {
                strengthIndicator.textContent = "Strength Profile: Moderate ⚡";
                strengthIndicator.style.color = "#FFA500";
            } else {
                strengthIndicator.textContent = "Strength Profile: Secure Key Verified ✔️";
                strengthIndicator.style.color = "#008080";
            }
        });
    }
}); 