const loginForm = document.getElementById("login-form");
// Error message box
const errorDiv = document.getElementById("error-box");
const errorMsg = document.getElementById("error-msg");
const onlyNumbers = (value) => {
    console.log("fun");
    const re =/^[0-9]+$/;
    return re.test(value);
}

const displayError = (msg) => {
    errorDiv.style.display = "block";
    errorMsg.textContent = msg;
}

loginForm.addEventListener("submit", (e) => {
    const id = e.target.studentId.value
    const password = e.target.password.value;
    if (!onlyNumbers(id)) {
        displayError("ID number can only contain digits");
        e.preventDefault();
    }
    e.preventDefault();

    // } else if(!onlyCharacters(city)){
    //     e.preventDefault();
    //     displayError("City can contain only letters.")
    //     return;
    // e.preventDefault();
})