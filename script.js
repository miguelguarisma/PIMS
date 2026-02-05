function showForm(formId) {
    document.querySelectorAll(".form-box").forEach(formId => formId.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}