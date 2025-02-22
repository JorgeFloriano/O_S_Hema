document.addEventListener("DOMContentLoaded", function() {
    const json_list = document.getElementById("json_list");
    const json_list_value = json_list.value;
    const object = json_list.dataset.object; // Access data-object
    
    createList(json_list_value, object);
});

// Function to process the data
function createList(json_list, object) {
    // Parse the JSON string into a JavaScript array
    const materials = JSON.parse(json_list);

    // Use forEach to iterate over the materials
    materials.forEach(material => {
        console.log(`Id: ${material.id}, Description: ${material.description}, Quantity: ${material.quantity}, Unit: ${material.unit}`);
        createListItemElements(material.description, object, material.id, material.quantity, material.unit);
    });
    getValuesAndSetInput(object);
}
