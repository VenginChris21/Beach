function updateClock() {
    const now = new Date();
    const clockElem = document.getElementById("clock");
    if (clockElem) {
        clockElem.textContent = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', minute: '2-digit', second: '2-digit' 
        });
    }
}

setInterval(updateClock, 1000);
updateClock();
