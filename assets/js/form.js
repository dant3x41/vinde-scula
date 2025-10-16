(function () {
    const form = document.querySelector('.vinde-scula-form');

    if (!form) {
        return;
    }

    const steps = Array.from(form.querySelectorAll('.vinde-scula-form-step'));
    const progressItems = Array.from(form.querySelectorAll('.vinde-scula-progress-step'));
    let currentStep = 0;

    function updateSteps() {
        steps.forEach((step, index) => {
            step.classList.toggle('is-active', index === currentStep);
        });

        progressItems.forEach((progress, index) => {
            progress.classList.toggle('is-active', index === currentStep);
        });
    }

    function focusCurrentStep() {
        const activeStep = steps[currentStep];

        if (!activeStep) {
            return;
        }

        const firstInput = activeStep.querySelector('input, button, select, textarea');

        if (firstInput) {
            firstInput.focus({ preventScroll: false });
        }
    }

    function goToStep(index) {
        if (index < 0 || index >= steps.length) {
            return;
        }

        currentStep = index;
        updateSteps();
        focusCurrentStep();
    }

    form.addEventListener('click', (event) => {
        const nextButton = event.target.closest('.vinde-scula-next');
        const prevButton = event.target.closest('.vinde-scula-prev');

        if (nextButton) {
            const targetStep = Number(nextButton.dataset.next) - 1;
            goToStep(targetStep);
        }

        if (prevButton) {
            const targetStep = Number(prevButton.dataset.prev) - 1;
            goToStep(targetStep);
        }
    });

    form.addEventListener('submit', () => {
        form.classList.add('is-submitting');
    });

    // Initialize first step visibility
    updateSteps();
    focusCurrentStep();
})();
