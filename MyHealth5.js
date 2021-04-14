function switchPage(page){
    const pages = ["loginPageContainer", "registerPageContainer", "patientPageContainer", "doctorPageContainer"];
    console.log(document.getElementsByClassName("pageContainer"));
    for(i of document.getElementsByClassName("pageContainer")){
        console.log(i);
        console.log(i.id);
        i.style.display = page == i.id ? "block" : "none";
    }
}