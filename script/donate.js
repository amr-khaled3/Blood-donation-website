document.addEventListener('DOMContentLoaded', function () {
    const backToTopBottom = document.getElementById("back-to-top");
    const bloodTypes = document.getElementById("bloodTypes");
    const triggerPosition = 400;

    window.addEventListener('scroll', () => {
        const scrollPosition = window.scrollY;

        if (scrollPosition >= triggerPosition) {
            bloodTypes.classList.add('visible');
        } else {
            bloodTypes.classList.remove('visible');
        }
        const elments = document.querySelectorAll('.slide');
        const windowHeight = window.innerHeight;
        elments.forEach((element)=>{
            const elementTop = element.getBoundingClientRect().top;
            if(elementTop > windowHeight - 15){
                element.classList.add('heddin');
            }else{
                element.classList.remove('heddin');
            }
        });
        if(window.scrollY > 500){
            backToTopBottom.style.display = 'block';
        }else{
            backToTopBottom.style.display = 'none';
        }
    });
    //? back-to-up bottom
    backToTopBottom.addEventListener('click', () =>{
        window.scrollTo({
            top: 0,
            bottom: 0,
            behavior: 'smooth'
        });
    });
    const medication2 = document.getElementById("Q2");
    const medication3 = document.getElementById("Q3");
    const additionalQuestion2 = document.getElementById("additionalQuestion2");
    const additionalQuestion3 = document.getElementById("additionalQuestion3");

    
    medication2.addEventListener('change', function () {
        if (medication2.value === 'Yes') {
            additionalQuestion2.classList.remove('heddinQ');
        } else {
            additionalQuestion2.classList.add('heddinQ');
        }
    });
    medication3.addEventListener('change', function () {
        if (medication3.value === 'Yes') {
            additionalQuestion3.classList.remove('heddinQ');
        } else {
            additionalQuestion3.classList.add('heddinQ');
        }
    });
     // Select form and input elements
    // const form = document.getElementById('form');
    const but = document.getElementById('but');
    const name = document.getElementById('name');
    const phone = document.getElementById("phone");
    const Email = document.getElementById("Email");
    const Gender = document.getElementById("Gender");
    const age = document.getElementById("age");
    const governorate = document.getElementById("governorate");
    const donationType = document.getElementById("donation-type");
    const bloodGroup = document.getElementById("blood-group");
    const Q1 = document.getElementById("Q1");
    const Q2 = document.getElementById("Q2");
    const q2 = document.getElementById("q2");
    const Q3 = document.getElementById("Q3");
    const q3 = document.getElementById("q3");

    console.log("1" + name.value);
    // Add submit event listener
    but.onclick = function (){
        console.log( "2" + name.value);
        checkInputs(); // Call validation function
    };

    // Validation function
    function checkInputs() {
        console.log("3" + name.value);
        // Get trimmed values from inputs
        const nameValue = name.value.trim();
        const phoneValue = phone.value.trim();
        const EmailValue = Email.value.trim();
        const ageValue = age.value.trim();
        const GenderValue = Gender.value;
        const governorateValue = governorate.value;
        const donationTypeValue = donationType.value;
        const bloodGroupValue = bloodGroup.value;
        const Q1Value = Q1.value;
        const Q2Value = Q2.value;
        const q2Value = q2?.value.trim();
        const Q3Value = Q3.value;
        const q3Value = q3?.value.trim();
        console.log("4" + nameValue);

        // Full Name validation
        if (nameValue === '') {
            console.log("5" + nameValue);
            setErrorFor(name, "Please enter your full name.");
        } else {
            setSuccessFor(name);
        }

        // Phone validation
        if (phoneValue === '') {
            setErrorFor(phone, 'Please enter your phone number.');
        } else {
            setSuccessFor(phone);
        }
        if (ageValue === '') {
            setErrorFor(age, 'Please enter your age.');
        } else {
            setSuccessFor(age);
        }

        // Email validation
        if (EmailValue === '') {
            setErrorFor(Email, 'Please enter your email.');
        } else if (!isValidEmail(EmailValue)) {
            setErrorFor(Email, 'Please enter a valid email address.');
        } else {
            setSuccessFor(Email);
        }

        // Gender validation
        if (GenderValue === '') {
            setErrorFor(Gender, 'Please select your gender.');
        } else {
            setSuccessFor(Gender);
        }

        // Governorate validation
        if (governorateValue === '') {
            setErrorFor(governorate, 'Please select your governorate.');
        } else {
            setSuccessFor(governorate);
        }

        // Donation Type validation
        if (donationTypeValue === '') {
            setErrorFor(donationType, 'Please select your donation type.');
        } else {
            setSuccessFor(donationType);
        }

        // Blood Group validation
        if (bloodGroupValue === '') {
            setErrorFor(bloodGroup, 'Please select your blood group.');
        } else {
            setSuccessFor(bloodGroup);
        }

        // Q1 validation
        if (Q1Value === '') {
            setErrorFor(Q1, 'Please answer this question.');
        } else {
            setSuccessFor(Q1);
        }

        // Q2 validation with conditional input
        if (Q2Value === '') {
            setErrorFor(Q2, 'Please answer this question.');
        } else {
            setSuccessFor(Q2);
            if (Q2Value === 'Yes' && q2Value === '') {
                setErrorFor(q2, 'Please specify the medication.');
            } else if (Q2Value === 'NO'){
                setSuccessFor(Q2);
                q2.classList.add('heddinQ');
            }
            else{
                setSuccessFor(Q2);
            }
        }

        // Q3 validation with conditional input
        if (Q3Value === '') {
            setErrorFor(Q3, 'Please answer this question.');
        } else {
            setSuccessFor(Q3);
            if (Q3Value === 'Yes' && q3Value === '') {
                setErrorFor(q3, 'Please specify your condition.');
            } else if (Q3Value === 'NO'){
                setSuccessFor(Q3);
                q3.classList.add('heddinQ');
            }
            else{
                setSuccessFor(q3);
            }
        }
    }

    // Helper functions
    function setErrorFor(input, message) {
        console.log("this is massage" + message)
        const formGroup = input.parentElement;
        const small = formGroup.querySelector('small');
        formGroup.className = 'form-group error';
        small.innerText = message;
    }

    function setSuccessFor(input) {
        const formGroup = input.parentElement;
        formGroup.className = 'form-group success';
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

});
