var submit = document.getElementById('submit')
var input = document.getElementById('start-date');
if(submit != null){
    submit.addEventListener('click', (e) => {
        var inputValue = input.value;
        if (inputValue == null || inputValue == ""){
            e.preventDefault();
            input.style.border = "1px solid red";
        }
    })
}
