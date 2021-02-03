let inputs = document.getElementsByTagName("input");
let desc = document.getElementById("desc");

for (let inp of inputs) {
    inp.onclick = () => {
        giveFeedback(inp);
    };
    if (inp["name"] == "confpassword") {
        inp.oninput = () => {
            checkCnfPwd();
        };
    }
}

function giveFeedback(frminput) {
    if (frminput["name"] == "uname") {
        desc.innerHTML =
            "Usernames: <ul><li>Must be unique</li><li>Must be between 4 and 20 characters in length</li><li>Can only contain letters and numbers</li><li>Cannot contain special characters</li></ul>";
    } else if (
        frminput["name"] == "password" ||
        frminput["name"] == "confpassword"
    ) {
        desc.innerHTML =
            "Passwords: <ul><li>Must be at least 8 characters</li><li>Must contain at least 1 uppercase letter, 1 lowercase letter, and 1 number</li><li>May contain special characters</li></ul>";
    } else if (frminput["name"] == "fname") {
        desc.innerHTML = "Please enter your first name";
    } else if (frminput["name"] == "sname") {
        desc.innerHTML = "Please enter your last name";
    } else {
        desc.innerHTML = "";
    }
}

function checkCnfPwd() {
    let pwd = document.getElementsByName("password")[0].value;
    let confpwd = document.getElementsByName("confpassword")[0].value;
    if (pwd != confpwd) {
        document.getElementsByName("confpassword")[0].className =
            "w3-input w3-border w3-red";
        desc.innerHTML = "Passwords do not match";
    } else {
        document.getElementsByName("confpassword")[0].className =
            "w3-input w3-border";
        desc.innerHTML = "Passwords match";
    }
}
