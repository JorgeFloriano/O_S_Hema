
function getScrollHeight(elm){
   var savedValue = elm.value
   elm.value = ''
   elm._baseScrollHeight = elm.scrollHeight
   elm.value = savedValue
 }

 window.onload = function() {
   var textareas = document.querySelectorAll('.autoExpand');
   for (var i = 0; i < textareas.length; i++) {
     onExpandableTextareaInput({ target: textareas[i] });
   }
 };
 
 function onExpandableTextareaInput({ target:elm }){
   // make sure the input event originated from a textarea and it's desired to be auto-expandable
   if( !elm.classList.contains('autoExpand') || !elm.nodeName == 'TEXTAREA' ) return
   
   var minRows = elm.getAttribute('data-min-rows')|0, rows;
   !elm._baseScrollHeight && getScrollHeight(elm)
 
   elm.rows = minRows
   rows = Math.ceil((elm.scrollHeight - elm._baseScrollHeight) / 20)
   elm.rows = minRows + rows
 }
 
 function blockScreen() {
   document.querySelector('html').style.overflow = "hidden";
 }

 function unlockScreen() {
   document.querySelector('html').style.overflow = "";
 }

 function limitDate(d, hi, hf) {
   var date = document.getElementById(d);
   var hini = document.getElementById(hi);
   var hfin = document.getElementById(hf);
   var z_left_h = "";
   var z_left_m = "";
   dataAtual = new Date();
   const hor = dataAtual.getHours();
   const min = dataAtual.getMinutes();
   if (date.value == date.max) {
      if (hor < 10) {
         z_left_h = "0";
      }
      if (min < 10) {
         z_left_m = "0";
      }
      hini.max = z_left_h + hor + ":" + z_left_m + min;
      hfin.max = z_left_h + hor + ":" + z_left_m + min;
   } else {
      hini.max = '';
      hfin.max = '';
   }
 }

//border-color:#fe8686;outline:0;box-shadow:0 0 0 .25rem rgba(253, 13, 13, 0.25)

// form submit---------------------------------------------------------------------------------------------------------------------------
function formSubmit(form) {
   document.getElementById(form).submit();
}
 
function scrollToBottom() {
   var height = document.body.scrollHeight;
   window.scroll(0 , height);
}

// Get address-----------------------------------------------------------------------------------------------------------------------------
function getAddress() {
   let cep = document.querySelector('#cep').value
   if (cep.length !== 8) {
       alert('Cep inválido !')
       return
   }
   let url = `https://viacep.com.br/ws/${cep}/json/`
   fetch(url).then(function(response){
       response.json().then(showAddress)  
   })
}

// CEP-------------------------------------------------------------------------------------------------------------------------------------
function showAddress(dados) {
   console.log(dados)
   if (dados.erro) {
       window.alert("Cep não encontrado")
       return
   }
   document.querySelector('#address').value = dados.logradouro+', '+dados.bairro+', '+dados.localidade+' - '+dados.uf;
}

function enableDisable(id) {
   div = document.getElementById(id);
   if (div.disabled == false) {
      this.checked = false;
      div.checked = false;
      div.disabled = true;
   } else {
      this.checked = true;
      div.disabled = false;
   }
}

// Toggle client fields----------------------------------------------------------------------------------------------------------------------
function toggleClientFields() {
   const saveRadio = document.getElementById('save');
   const finishedRadio = document.getElementById('finished');
   const clientFieldsDiv = document.getElementById('clientFields');
 
   if (finishedRadio.checked) {
     clientFieldsDiv.style.display = 'block';
     window.scrollTo(0, clientFieldsDiv.offsetTop); // Add this line
   } else {
     clientFieldsDiv.style.display = 'none';
   }
 }

 // global delegated event listener
 document.addEventListener('input', onExpandableTextareaInput)

 // input datalist client get only client id, not name, acept only datalist option values---------------------------------------------------
 function getOptId(inp_list, data_id, data_opt, index) {
   const inp_list_val = document.getElementById(inp_list).value;


   if (inp_list_val == 'Todos - []') {
      document.getElementById(data_id).value = 'Todos - []'
      document.getElementById(inp_list).value = 'Todos - []'
      return
   }

   if (inp_list_val == '') {
      document.getElementById(data_id).value = '0'
      document.getElementById(inp_list).value = ''
      if (index == '' || index == null) {
         return
      }
   }

   const matches = inp_list_val.match(/\[(\d+)\](?!.*\[\d+\])/);
   const data_options = window.document.getElementsByClassName(data_opt);
   var opt_valid = false;

   for (let data_opt of data_options) {
      if (data_opt.value == inp_list_val) {
         opt_valid = true
      }
   }
   
   if (!opt_valid || !matches) {
      document.getElementById(data_id).value = '0'
      document.getElementById(inp_list).value = ''
      if (index == '' || index == null) {
         return
      }
   }

   var object_selected_id = 0
   if (matches != null) {
      object_selected_id = matches[1]
   }

   document.getElementById(data_id).value = object_selected_id

   if (index != '' && index != null) {

      // Update tec_id in order , without reload page with fetch
      const orderId = index;
      const tecId = object_selected_id;

      fetch(`/Hema/public/orders/${orderId}/ord_tec_update`, {
         method: 'POST',
         headers: {
               'Content-Type': 'application/json',
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Captura o token CSRF
         },
         body: JSON.stringify({ tec_id: tecId })
      })
      
      .catch(error => {
         console.error('Erro:', error);
         alert('Erro ao atualizar técnico.');
      });
   }
 }
 
 // Manage list-----------------------------------------------------------------------------------------------------------------
 function manageList(object) {
   const input = document.getElementById(object);
   const descr = input.value;

   // Verify if object is already added
   if (document.querySelector('.'+object +'-added-descr') !== null) {
      const options = document.getElementsByClassName(object +'-added-descr');
      const regex = / - \[\d+\]$/;
      const descr_whichout_id = descr.replace(regex, '');
      
      for (let option of options) {
         if (option.innerHTML == descr_whichout_id ) {
            document.getElementById(object).value = '';
            document.getElementById(object).blur();
            alert('Ítem já adicionado, utilize o campo numérico para determinar a quantidade, digitando zero ou deixando o campo vazio o ítem será removido !')
            return
         }
      }
   }

   // Verify if object description is empty, return
   if ( descr == '' ) {
      document.getElementById(object).blur(); // remove focus
      return
   }

   // Instace variables
   const inp_list_val = document.getElementById(object).value;
   const matches = inp_list_val.match(/\[(\d+)\](?!.*\[\d+\])/);
   const obj_selected_id = matches[1]; // object_selected_id 
   const obj_selected_unit = document.getElementById(obj_selected_id+'_unit').value; // object_selected_unit
   const form = document.getElementById('form');

   // Call the function to create elements to constitute the datalist items
   createListItemElements(descr, object, obj_selected_id, obj_selected_unit, 'serv');
   
   document.getElementById(object).blur();// remove focus
   getValuesAndSetInput(object);// Call the function to get values and set them in the result input
 }

 // Function to get values and set them in the result input---------------------------------------------------------------
 function getValuesAndSetInput(object) {
   // Select all elements that was added in list
   const elements = document.querySelectorAll('.'+object+'-added-id');

   // Extract their values into an array
   const values = Array.from(elements).map(element => element.value);

   // Join the values into a comma-separated string
   const commaSeparatedValues = values.join(',');

   // Set the result in the target input field
   document.getElementById(object+'_ids_array').value = commaSeparatedValues;
}

// Function to create elements to constitute the datalist items------------------------------------------------------------
function createListItemElements(descr, object, obj_selected_id, obj_selected_unit) {
   // Create div from the object selected
   const div = document.createElement('div');
   const object_list = document.getElementById(object+'_list');
   object_list.style.background = '#e9ecef';
   object_list.style.border = '1px solid #ced4da';
   object_list.style.borderRadius = '.25rem';
   object_list.classList.add('my-2');
   div.classList.add('input-group');
   div.id = object +'_'+obj_selected_id+'_div';
   object_list.appendChild(div);

   // Add span with object description------------------------------------------------------------------------------------
   const regex = / - \[\d+\]$/;
   const descr_whichout_id = descr.replace(regex, '');
   const objDscr = document.createElement('span');
   objDscr.classList.add('input-group-text');
   objDscr.classList.add(object +'-added-descr');
   objDscr.innerHTML = descr_whichout_id ;
   objDscr.style.width = '70%';
   objDscr.style.background = 'transparent';
   objDscr.style.border = 'none';
   div.appendChild(objDscr);
   objDscr.id = object +'_'+obj_selected_id+'_descr';

   // Add input with object quantity--------------------------------------------------------------------------------------
   const objInput = document.createElement('input');
   objInput.type = 'number';
   objInput.name = object +'_'+obj_selected_id+'_qtd';
   objInput.classList.add('form-control');
   objInput.classList.add(object +'-added-qtd');
   objInput.style.border = 'none';
   objInput.placeholder = 'Quantidade';
   objInput.required = true;
   objInput.autocomplete = 'off';
   objInput.value = 1;
   objInput.min = 0;
   objInput.id = object +'_'+obj_selected_id+'_qtd';
   div.appendChild(objInput);

   // Add input hidden with object id in value---------------------------------------------------------------------------
   const objSelectedId = document.createElement('input');
   objSelectedId.type = 'hidden';
   objSelectedId.name = object +'_'+obj_selected_id+'_id';
   objSelectedId.classList.add(object +'-added-id');
   objSelectedId.value = obj_selected_id;
   objSelectedId.min = 0;
   objSelectedId.id = object +'_'+obj_selected_id+'_id';
   div.appendChild(objSelectedId);

   // Add span with unit of measurement of the object------------------------------------------------------------------
   const objUnit = document.createElement('span');
   objUnit.classList.add('input-group-text');
   objUnit.innerHTML = obj_selected_unit;
   objUnit.style.width = '12%';
   objUnit.style.background = 'transparent';
   objUnit.style.border = 'none';
   objUnit.id = object +'_'+obj_selected_id+'_unit';
   div.appendChild(objUnit);

   // Add an event listener to remove all elements when the input value is zero or empty
   objInput.addEventListener('change', function () {
      if (this.value == 0 || this.value == '') { // Check if the value is zero
         div.remove(); // Remove the div element
         objDscr.remove(); // Remove the span with object description
         objInput.remove(); // Remove the input with object quantity
         objUnit.remove(); // Remove the span with unit of measurement
         objSelectedId.remove(); // Remove the input hidden with object id

         // Select all elements that was added in list
         const elements = document.querySelectorAll('.'+object+'-added-id');
         if (elements.length < 1) {
            const object_list = document.getElementById(object+'_list');
            object_list.style.background = 'none';
            object_list.style.border = 'none';
            object_list.style.borderRadius = 'none';
            object_list.classList.remove('my-2');
        }
         getValuesAndSetInput(object);// Call the function to get values and set them in the result input
      }
   });
}

// Function to convert string to array
function strToArr(inputString) {
   alert('test');
   // Step 1: Remove the outer brackets
   const trimmedString = inputString.slice(2, -2);

   // Step 2: Split by "],[" to separate the inner arrays
   const innerArrays = trimmedString.split("],[");

   // Step 3: Map through the inner arrays, clean them up, and split by commas
   const result = innerArrays.map((item) => {
      // Remove any remaining brackets and trim whitespace
      const cleanedItem = item.replace(/[\[\]]/g, "").trim();
      // Split by commas and trim each element
      cleanedItem.split(",").map((element) => element.trim());
      alert('test');
   });
}
function submitRoute(route, form, msg) {
   if (msg != null && msg != '') {
      alert(msg);
      return
   }
   document.getElementById(form).action = route;
   document.getElementById(form).submit();
}

function clearInputs(input, inputHidden) {
   document.getElementById(input).value = '';
   document.getElementById(inputHidden).value = '0';
}
