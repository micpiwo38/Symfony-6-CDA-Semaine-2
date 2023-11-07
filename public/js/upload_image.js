document.addEventListener("DOMContentLoaded", () => {


    const addFormToCollection = (e) => {
        const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
        console.log(collectionHolder)
        const item = document.createElement('div');

        item.innerHTML = collectionHolder
            .dataset
            .prototype
            .replace(
                /__name__/g,
                collectionHolder.dataset.index
            );

        //item.querySelector('#btn-supprimer').addEventListener('click', () => item.remove());
        let btnSupprimer = document.createElement('button');
        btnSupprimer.className = 'btn btn-danger btn-supprimer';
        btnSupprimer.id = 'btn-supprimer';
        btnSupprimer.innerHTML = 'Supprimer';
        item.appendChild(btnSupprimer);


        collectionHolder.append(item);
        collectionHolder.dataset.index++;

        document.querySelectorAll('.btn-supprimer').forEach(btn => btn.addEventListener('click', (e) =>
            e.currentTarget.parentElement.remove()));

    };

    //



    document.querySelectorAll('.btn-ajouter').forEach(btn => btn.addEventListener('click', addFormToCollection));
});