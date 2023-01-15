// Elements
link = document.querySelector(".add-another-link");
cardBody = document.querySelector(".card-body");
SubmitBtn = document.querySelector("input[type=submit]");
// Data
choiceNum = 3;



link.addEventListener('click', function (e) {
    const newLabel = document.createElement(`label`);
    newLabel.classList.add("card-choice-textarea");
    newLabel.innerHTML = `        <br>
    <input type="radio" disabled>
    <textarea name=choice${choiceNum++} type="button" class="other-choice" placeholder="Enter other choice"></textarea>
    <i class="fas fa-trash"></i>
    `;

    cardBody.insertBefore(newLabel, cardBody.children[cardBody.children.length - 1]);
});
cardBody.addEventListener('click', function (e) {
    if (e.target.matches("i")) {
        console.log(e.target.parentNode);
        e.target.parentNode.remove();
        choiceNum = choiceNum - 1;
    }
});


// SubmitBtn.addEventListener('onclick', function(e){

// });