class Condition{
    constructor(){
        this.approuver = document.getElementById('approuver');
        this.valider = document.getElementById('valider');
        this.approuver.addEventListener("click", () => {this.afficher();});
    }
    
    afficher() {

        this.valider.style.visibility= "visible";
    }
}
new Condition();