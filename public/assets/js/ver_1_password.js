/**----------------------------------
 * SCRIPT PARA UN SOLO PASSWORD
------------------------------------*/

function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.querySelector(".toggle-password");
    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";
    icon.classList.toggle("fa-eye-slash");
    icon.classList.toggle("fa-eye");
}