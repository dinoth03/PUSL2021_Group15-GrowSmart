function filterPlants() {
    let input = document.getElementById('searchBox').value.toLowerCase();
    let plants = document.getElementsByClassName('plant');

    for (let plant of plants) {
        let name = plant.dataset.name.toLowerCase();
        plant.style.display = name.includes(input) ? 'block' : 'none';
    }
}

let preveiwContainer = document.querySelector('.plant-guide');
let preveiwBox = preveiwContainer.querySelectorAll('.guide');

document.querySelectorAll('#plant-list .plant').forEach(plant =>{
    plant.onclick = () =>{
        preveiwContainer.style.display = 'flex';
        let name = plant.getAttribute('data-name');
        preveiwBox.forEach(guide =>{
            let target = guide.getAttribute('data-target');
            if(name == target){
                guide.classList.add('active');
            }
        });
    };
});

preveiwBox.forEach(close =>{
    close.querySelector('.btn-close').onclick = () =>{
        close.classList.remove('active');
        preveiwContainer.style.display = 'none';
    };
});