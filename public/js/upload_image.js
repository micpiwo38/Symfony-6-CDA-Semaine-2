document.addEventListener("DOMContentLoaded", () => {

    const addFormToCollection = (e) => {
        const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
        console.log(collectionHolder)

        const item = document.createElement('div');
        item.className = 'mt-3';

        item.innerHTML = collectionHolder
            .dataset
            .prototype
            .replace(
                /__name__/g,
                collectionHolder.dataset.index
            );

        let btnSupprimer = document.createElement('button');
        btnSupprimer.className = 'btn btn-danger mt-3 btn-supprimer';
        btnSupprimer.id = 'btn-supprimer';
        btnSupprimer.innerHTML = 'Supprimer';
        item.appendChild(btnSupprimer);

        collectionHolder.append(item);
        collectionHolder.dataset.index++;

        document.querySelectorAll('.btn-supprimer').forEach(btn => btn.addEventListener('click', (e) =>
            e.currentTarget.parentElement.remove()));

    };

    document.querySelectorAll('.btn-ajouter').forEach(btn => btn.addEventListener('click', addFormToCollection));
});


//Supprimer des images

//Récuperer chaque bouton de suppression a l'aide de l'attribut data-delete
let btnSupprimer = document.querySelectorAll('[data-delete]');
console.log(btnSupprimer);
//Parcourir le tableau de boutons de suppression
for (let btn of btnSupprimer) {
    //Ajouter un evenement de clic 
    //Je n'utilise pas volontairement les fonction fléchée pour garder le context avec this
    btn.addEventListener('click', function (e) {
        //Modifier le comportement par defaut d'un bouton
        //Evite le refresh de la page
        e.preventDefault();
        console.log('ok : test de clic');
        //Popup de confirmation de supression d'une image
        if (confirm("Voulez-vous vraiment supprimer cette image ?")) {
            //Requete HTTP DELETE + configuration de l'entete
            //Recuperer les attributs href des boutons (qui appel la route du controller)
            //A l'aide de l'API fetch qui fournit une interface JavaScript pour accéder 
            //et manipuler certaines parties du protocole,
            fetch(this.getAttribute('href'), {
                //Configuration de la requète
                method: 'DELETE',
                //Entete de la requète = Cette requète utilise le format json
                headers: {
                    "Content-Type": "application/json"
                },
                //Corp de la requète = recupération de l'attribut data-token sous forme de chaine de caractères
                body: JSON.stringify({ '_token': this.dataset.token })
            })
                //La promesse ou echec
                //Réponse au format json
                .then(response => response.json())
                //Status de la requète 
                .then(data => {
                    //Si la requète reussis
                    if (data.success) {
                        //On suprimme le parent du DOM
                        //Le service va également spécifié le changement de status et envoi au contoller
                        //L'ordre de supression
                        this.parentElement.remove();
                        console.log(data.success);
                    } else {
                        //Sinon on afficher une alerte d'erreur
                        alert(data.error);
                    }
                })
                //Debug si la requète echoue
                .catch(erreur => console.error(erreur));
        }
    });
}

