// Navigation
const courseDropdownParent = document.getElementById("course-dropdown-parent")
const courseDropdownList = document.getElementById("course-dropdown-list")
const courseDropdownNavItems = document.querySelectorAll(".dropdown-nav-item");
// Listeners
courseDropdownParent.addEventListener("mouseover", (e) => {
    courseDropdownList.style.display = "initial";
})
courseDropdownParent.addEventListener("focus", (e) => {
    courseDropdownList.style.display = "initial";
})
courseDropdownNavItems[courseDropdownNavItems.length - 1].addEventListener("blur", (e) => {
    courseDropdownList.style.display = "none";
})
courseDropdownList.addEventListener("mouseleave", (e) => {
    // Dropdown disappears after mouse had left the box
    e.target.style.display = "none";
})


// Main form
const form = document.getElementById("main-form");
// Error message box
// const errorDiv = document.getElementById("error-box");
// const errorMsg = document.getElementById("error-msg");
// Get input fields
const telephoneNumberInput = document.getElementById("telephoneNo");

const displayError = (msg) => {
    errorDiv.style.display = "block";
    errorMsg.textContent = msg;
}

// Fields validation
const validateTelephoneNumber = (number) => {
    if(number.length !== 11){
        displayError("Telephone number has to have 11 digits.")
        return false;
    }
    return true;
}

const onlyCharacters = (value) => {
    const re = /^[a-zA-Z\s]+$/;
    return re.test(value);
}

const isDateValid = (date, check) => {
    // Check defines if date should be set to before today date (-1) equals to today (0) or later (1)
    console.log(date);
}


// form.addEventListener("submit", (e) => {
//     const telephoneNumber = e.target.telephoneNo.value;
//     const city = e.target.city.value;
//     const county = e.target.county.value;
//     const country = e.target.country.value;
//     const currentDate = new Date();
//     if(!validateTelephoneNumber(telephoneNumber)){
//         e.preventDefault();
//         return;
//     } else if(!onlyCharacters(city)){
//         e.preventDefault();
//         displayError("City can contain only letters.")
//         return;
//     }  else if(!onlyCharacters(county)){
//         e.preventDefault();
//         displayError("County can contain only letters.")
//         return;
//     } else if (!onlyCharacters(country)){
//         e.preventDefault();
//         displayError( "Country can contain only letters.")
//         return;
//     }
//     if(e.target.lastDate !== undefined && e.target.resumeDate !== undefined){
//         const lastDate = new Date(e.target.lastDate.value);
//         const resumeDate = new Date(e.target.resumeDate.value);
//         if(lastDate.getTime() >= resumeDate.getTime()){
//             e.preventDefault();
//             displayError("Last date has to be before resume date.")
//             return;
//         }
//     }
//     if(e.target.examDate !== undefined){
//         const examDate = new Date(e.target.examDate.value)
//         if(exameDate.getTime() <= currentDate.getTime()){
//             e.preventDefault();
//             displayError("Exam date has to be in the future.")
//             return;
//         }
//     }
// })
