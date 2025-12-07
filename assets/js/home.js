document.addEventListener("DOMContentLoaded", function() {

    // --- Helper: Set active navbar link ---
    function setActiveLink(targetLink) {
        document.querySelectorAll('#navbarNav .nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.id !== 'showAdminFormLink') link.classList.remove('btn','btn-admin-nav');
        });
        if(targetLink) targetLink.classList.add('active');
    }
    window.setActiveLink = setActiveLink;

    // --- Helper: Toggle forms (Unchanged, uses CSS classes 'hidden' and transitions) ---
    function toggleForm(id) {
        const forms = ["loginForm","registerForm","adminLoginForm"];
        forms.forEach(f => {
            const form = document.getElementById(f);
            if(!form) return;

            if(f === id) {
                form.classList.remove('hidden');
                form.style.opacity = 0;
                form.style.transform = "translateY(30px)";
                setTimeout(() => {
                    form.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                    form.style.opacity = 1;
                    form.style.transform = "translateY(0)";
                }, 10);
                form.scrollIntoView({behavior:'smooth', block:'center'});
            } else {
                form.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                form.style.opacity = 0;
                form.style.transform = "translateY(30px)";
                setTimeout(() => form.classList.add('hidden'), 500);
            }
        });
    }
    window.toggleForm = toggleForm;

    // --- Helper: Display messages (UPDATED to use Bootstrap classes) ---
    function displayResponse(divId, message, isError) {
        const div = document.getElementById(divId);
        if (!div) return;
        
        div.innerText = message;
        div.style.display = 'block'; // Make sure the div is visible
        
        // Remove old classes
        div.classList.remove('alert', 'alert-success', 'alert-danger', 'alert-info');
        
        // Apply new Bootstrap alert classes
        div.classList.add('alert');
        if (isError) {
            div.classList.add('alert-danger'); // Red background for errors
        } else if (message.includes('Logging in') || message.includes('Processing')) {
            div.classList.add('alert-info'); // Blue background for loading/info
        } else {
            div.classList.add('alert-success'); // Green background for success
        }

        // Apply fade-in effect
        div.style.opacity = 0;
        setTimeout(() => { 
            div.style.transition = 'opacity 0.5s'; 
            div.style.opacity = 1; 
        }, 10);
    }

    // --- Auto show login form ---
    const loginLink = document.getElementById('showLoginFormLink');
    if(loginLink){ 
        toggleForm('loginForm'); 
        setActiveLink(loginLink); 
    }

    // ----------------------------------------------------------------------------------
    // --- 1. Employee Login ---
    // ----------------------------------------------------------------------------------
    const loginForm = document.getElementById('loginForm');
    if(loginForm){
        loginForm.addEventListener('submit', async function(e){
            e.preventDefault();
            const formData = new FormData(this);
            displayResponse('loginResponse','Logging in...',false);

            try{
                const res = await fetch('../handlers/login.php', { method:'POST', body: formData });
                const result = await res.json();

                if(result.success && result.redirect){
                    displayResponse('loginResponse',result.success+' Redirecting...',false);
                    // *** CORRECTION APPLIED HERE ***
                    // result.redirect already contains the full path like 'pages/admin_dashboard.php'
                    setTimeout(() => window.location.href = '../' + result.redirect, 500); 
                } else {
                    displayResponse('loginResponse', result.error || 'Login failed', true);
                }
            } catch(err){
                console.error(err);
                displayResponse('loginResponse','A network error occurred or invalid server response.', true);
            }
        });
    }

    // ----------------------------------------------------------------------------------
    // --- 2. Employee Registration ---
    // ----------------------------------------------------------------------------------
    const registerForm = document.getElementById('registerForm');
    if(registerForm){
        registerForm.addEventListener('submit', async function(e){
            e.preventDefault();
            const formData = new FormData(this);
            displayResponse('registerResponse','Processing registration...',false);

            try{
                const res = await fetch('../handlers/signup.php', { method:'POST', body: formData });
                const result = await res.json();

                if(result.success && result.redirect){
                    displayResponse('registerResponse', result.success+' Redirecting...',false);
                    setTimeout(() => window.location.href = '../'+result.redirect, 1000);
                } else {
                    displayResponse('registerResponse', result.error || 'Registration failed', true);
                }
            } catch(err){
                console.error(err);
                displayResponse('registerResponse','A network error occurred or invalid server response.', true);
            }
        });
    }

    // ----------------------------------------------------------------------------------
    // --- 3. Admin Login ---
    // ----------------------------------------------------------------------------------
    const adminForm = document.getElementById('adminLoginForm');
    if(adminForm){
        adminForm.addEventListener('submit', async function(e){
            e.preventDefault();
            const formData = new FormData(this);
            displayResponse('adminResponse','Logging in...',false);

            try{
                const res = await fetch('../handlers/admin_login.php', { method:'POST', body: formData });
                const result = await res.json();

                if(result.success && result.redirect){
                    displayResponse('adminResponse', result.success+' Redirecting...',false);
                    setTimeout(() => window.location.href = '../'+result.redirect, 500);
                } else {
                    displayResponse('adminResponse', result.error || 'Admin Login failed', true);
                }
            } catch(err){
                console.error(err);
                displayResponse('adminResponse','A network error occurred or invalid server response.', true);
            }
        });
    }
});