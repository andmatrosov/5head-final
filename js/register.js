document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.querySelector('.form__register');
    window.nickname = null;
    window.email = null;
    window.isFinished = false;


    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const registerModal = document.getElementById('register')
        const target = e.target;
        const data = new FormData(target);

        const { nickname, email } = Object.fromEntries(data.entries());

        try {
            await fetch('/backend/api/register-participant.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nickname,
                    email
                })
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) {
                    throw new Error(`HTTP error! status: ${data.success}`, { cause: data.message });
                }

                console.log("Data fetched successfully:", data);
                localStorage.setItem('userId', data.participant_id);


                if(window.sounds.isPlaying('start')) {
                    setTimeout(() => {
                        window.sounds.fadeOut('start', 500)

                        registerModal.close();
                        window.quizInstance = new Quiz();
                    }, 1000)
                } else {
                    registerModal.close()
                    window.quizInstance = new Quiz();
                }
            });

        } catch (e) {
            const errorEl = registerForm.querySelector('.error_message');
            errorEl.textContent = e.cause;
            errorEl.classList.add('show');

            const timeout = setTimeout(() => {
                errorEl.textContent = '0';
                errorEl.classList.remove('show');
                clearTimeout(timeout);
            }, 5000)
        }

        localStorage.setItem('isFinished', false);
    });

})