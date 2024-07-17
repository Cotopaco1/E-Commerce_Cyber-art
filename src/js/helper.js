//Funciones helper.

function activar_evento_submit_by_id(evento, formulario = 'formulario'){
    //Creo evento de submit y se lo aplico al formulario...
    const form = document.getElementById(formulario);
    const event = new Event('submit', {
        'bubbles': true,
        'cancelable': true
    });
    form.dispatchEvent(event);
}

function capturar_y_verificar_datos_de_formulario(event){
    //capturar los datos del formulario en un objeto.
    event.preventDefault()
    const data = crear_objeto_con_datos_de_formulario(event)

    //verificar el objeto antes de mandarlo al back-end
    /* console.log();
    console.log(data); */
    for(const [key,value] of Object.entries(data)){
        if(value === ''){
            return false
        }
    }
    /* Object.entries(data).forEach(([key, value]) => {
        console.log(value);
        if(value === ''){
            console.log('Paso la validacion')
            return false
        }
        
    }); */
    return data;
    //Si en el objeto data hay algun campo que este vacio entonces...
    /* if(errorInput){
        resaltarErrores()
        return
    }
    crear_alerta_de_cargando();
    enviar_solicitud_login(data)
    .then(eliminar_alerta_de_cargando)
    .then(validar_respuesta_login) */
    
    //enviar solicitud de login al backend.

}
function crear_objeto_con_datos_de_formulario(event){
    const data = Object.fromEntries(new FormData(event.target))
    return data;
}

//Empieza alerta
function crear_alerta_de_cargando(){
    const modal = crear_modal_en_body()
    insertar_alerta_cargando(modal);
    //inserto alerta en el modal

}

function eliminar_alerta_de_cargando(resultado){
    //eliminar modal que contiene la alerta.
    const modal = document.getElementById('modal');
    modal.remove();
    return resultado
}

function crear_modal_en_body(){
    const modal = document.createElement('DIV');
    const body = document.querySelector('body')
    modal.classList.add('modal');
    modal.id = 'modal';
    body.appendChild(modal);
    return modal;
}

function insertar_alerta_cargando(modal){
    const alerta = document.createElement('DIV');
    alerta.classList.add('alertaCargando')
    alerta.innerHTML = `<i class="fa-solid fa-spinner fa-2xl"></i>`
    modal.appendChild(alerta);
}
//Termina alerta



export { activar_evento_submit_by_id, capturar_y_verificar_datos_de_formulario,
    crear_alerta_de_cargando,
    eliminar_alerta_de_cargando
};