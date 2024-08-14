document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();

})
//variables globales
let productos = [];
//termina variables globales

function iniciarApp(){
    cuenta_regresiva();
    slider();
    expandirCampo();
    oferta();
    peticion_get('/api/cuadros/getcuadros')
    .then(e=>{
        productos = e;
    })
    //evento del formulario
    document.querySelector('#formulario').addEventListener('submit', function(e){ 
        e.preventDefault();
        crear_alerta_cargando();
        setTimeout(() => {
            enviar_formulario(e);
            
        }, 1);
       
    } )
    //scroll hacia formulario
    document.querySelectorAll('.quiero_comprar').forEach(contenedor=>{
        contenedor.addEventListener('click', scroll_form);
    })

}
function cuenta_regresiva(){
    //obtener el tiempo restante.
    peticion_get('/api/lp/cuenta_regresiva')
    .then(respuesta=>{

        const {horas, minutos, segundos} = respuesta.cuenta_regresiva;

        let totalSegundos = (horas * 3600) + (minutos * 60) + segundos;
        let horas_p = Math.floor(totalSegundos / 3600);
        let minutos_p = Math.floor((totalSegundos % 3600) / 60);
        let segundos_p = totalSegundos % 60;

        document.getElementById('cuenta_regresiva__horas').textContent = `${horas_p}`
        document.getElementById('cuenta_regresiva__minutos').textContent = `${minutos_p}`
        document.getElementById('cuenta_regresiva__segundos').textContent = `${segundos_p}`

        //setinterval de 1s
        //actualizar el contador en pantalla cada segundo.
        const cuentaRegresa = setInterval(() => {
            let horas = Math.floor(totalSegundos / 3600);
            let minutos = Math.floor((totalSegundos % 3600) / 60);
            let segundos = totalSegundos % 60;

            document.getElementById('cuenta_regresiva__horas').textContent = `${horas}`
            document.getElementById('cuenta_regresiva__minutos').textContent = `${minutos}`
            document.getElementById('cuenta_regresiva__segundos').textContent = `${segundos}`

            if(totalSegundos <= 0){
                clearInterval(cuentaRegresa);
            }else{
                totalSegundos--;
            }
        }, 1000);
    })
}

function slider(){

    const sliderInner = document.querySelector('.slider__inner');
    /* sliderInner.style.transform = "translate(-200%)" */
    const cantidadImg = sliderInner.querySelectorAll('img').length;
    let index = 1;
    setInterval(() => {
        let num = index * -100;
        /* console.log(`translate(${num})`); */
        sliderInner.style.transform = `translate(${num}%)`;
        index++;
        if(index > cantidadImg -1){
            index = 0;
        }
    }, 1500);

}
function expandirCampo(){
    const camposFooter = document.querySelectorAll('.flex');
    camposFooter.forEach(function(campo){
        campo.addEventListener('click', event=>{
            const target = event.currentTarget.nextElementSibling
            const icono = event.currentTarget.firstElementChild;
            target.classList.toggle('mostrar_respuesta')

            if(target.classList.contains('mostrar_respuesta')){
                //ajustar altura.
                target.style.maxHeight = target.scrollHeight + 'px';
                //Cambiar el transform a la imagen.
                icono.style.transform = 'rotate(0.5turn)';
                
            }else{
                target.style.maxHeight = '0';
                icono.style.transform = 'rotate(0turn)';
            }

            setTimeout(() => {
                target.style.maxHeight = target.classList.contains('mostrar_respuesta') ? target.style.maxHeight = target.scrollHeight + 'px' : '0';
            }, 10);


        }) 
    })
}
function oferta(){
    document.querySelector('#oferta').addEventListener('input', event=>{
        const div_otro_cuadro =  document.querySelector('#otro_cuadro_div');
        const target = event.target;
        if(event.target.value === '2'){
            div_otro_cuadro.classList.add('mostrar_respuesta');

        }else{
            if(div_otro_cuadro.classList.contains('mostrar_respuesta')){
                div_otro_cuadro.classList.remove('mostrar_respuesta');
            }
            const inputCheckbox = document.querySelector('#otro_cuadro');
            if(inputCheckbox.checked){
                /* console.dir(document.querySelector('#otro_cuadro')) */
                inputCheckbox.checked = false;
                // Crear y despachar el evento 'change'
                const evento = new Event('change', { bubbles: true });
                inputCheckbox.dispatchEvent(evento);
            }
        }

        if(div_otro_cuadro.classList.contains('mostrar_respuesta')){
            //ajustar altura.
            div_otro_cuadro.style.maxHeight = div_otro_cuadro.scrollHeight + 'px';
            //Cambiar el transform a la imagen.
            
        }else{
            div_otro_cuadro.style.maxHeight = '0';
        }

        setTimeout(() => {
            div_otro_cuadro.style.maxHeight = div_otro_cuadro.classList.contains('mostrar_respuesta') ? div_otro_cuadro.style.maxHeight = div_otro_cuadro.scrollHeight + 'px' : '0';
        }, 10);

    })




    document.querySelector('#otro_cuadro').addEventListener('change', event=>{
        console.log('se ha activado el evento de input..')
        const checked = event.target.checked;
        const referencia = event.target.parentElement;
        /* console.dir(event.target.parentElement) */
        if(checked){
            /* const parrafo = document.createElement('P');
            parrafo.textContent = 'Selecciona la imagen del otro cuadro deseado';
            parrafo.classList.add('formulario_lp__texto')
            console.log(referencia); */
            //
            /* referencia.insertAdjacentElement('afterend', parrafo); */
            //mostrar opciones
            const div = document.createElement('DIV');
            div.classList.add('slider-manual')
            div.setAttribute('id','slider-manual');
            productos.forEach(producto=>{
                const label = document.createElement('LABEL');
                label.classList.add('slider-manual__label')

                const input = document.createElement('INPUT');
                input.type = 'radio';
                input.name = 'otro_producto_nombre';
                input.value = producto.nombre;
                input.classList.add('slider-manual__input')


                const img = document.createElement('IMG');
                img.src = 'img/productos/' + producto.imagen;

                label.appendChild(input);
                label.appendChild(img);
                
                div.appendChild(label);
            })
            console.log(div);
            /* div.textContent = 'Prueba prueba'; */

            referencia.insertAdjacentElement('afterend', div);

        }else{
            //borrar opciones si estan mostradas
            const slider = document.querySelector('.slider-manual');
            if(slider){
                slider.remove();
            }
        }

    })
}
async function enviar_formulario(e){
    const data = Object.fromEntries(new FormData(e.target))
    options = {
        method: 'post',
        body: JSON.stringify(data)
    }
    url = '/api/lp/enviar_formulario';
    try {
        //hacer fetch.
        
        const resultado = await fetch(url,options);
        const respuesta = await resultado.json();
        //boton cargando...
        if(respuesta.exito){
            //eliminar boton cargando...
            eliminar_alerta_cargando(true);
            Swal.fire({
                title: "Pedido creado!",
                text: "Tenemos tu pedido, pronto te contactaremos para confirmar y enviar tu pedido 😎",
                icon: "success"
              });
            //mostrar exito en pantalla.
        }else{
            //eliminar boton cargando...
            eliminar_alerta_cargando(false);
            mostrar_alerta_error(respuesta.error);

            //mostrar alertas en pantalla
        }
        
        
    } catch (error) {
        console.log(error)
    }

}


//helpers 
async function peticion_get(url){
    try {
        const resultado = await fetch(url);
        const respuesta = await resultado.json();
        return respuesta;
    } catch (error) {
        console.log(error)
    }
}
function crear_alerta_cargando(){
    const contenedor = document.querySelector('#contenedor_submit');
    const submit = document.querySelector('#submit');
    submit.style.display = 'none';

    const alerta = document.createElement('IMG');
    alerta.src = 'img/iconos/loading.svg';
    alerta.classList.add('alerta--cargando');

    contenedor.appendChild(alerta);
    
}
function eliminar_alerta_cargando(tipo){
    document.querySelector('.alerta--cargando').remove();
    if(!tipo){
        document.querySelector('#submit').style.display = 'block';
        return;
    }
    const contenedor = document.querySelector('#contenedor_submit');
    const alerta = document.createElement('IMG');
    alerta.src = 'img/iconos/check.svg';
    alerta.classList.add('alerta--check');
    contenedor.appendChild(alerta);

}
function mostrar_alerta_error(alertas){
    //eliminar alertas anteriores
    document.querySelectorAll('.mensaje-error').forEach(mensaje=>{
        mensaje.remove();
    })
    alertas.forEach(alerta=>{
        const mensaje = document.createElement('P');
        mensaje.textContent = '**Este campo es obligatorio** ↓';
        mensaje.classList.add('mensaje-error');



        document.querySelector(`#${alerta}`).insertAdjacentElement('beforebegin', mensaje);
        
    })
    //desplazar la vista del usuario al primer error
    document.querySelector('.mensaje-error').scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
    })
    setTimeout(() => {
        document.querySelectorAll('.mensaje-error').forEach(mensaje=>{
            mensaje.remove();
        })
    }, 4000);

}
function scroll_form(){
    document.querySelector('#formulario').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    })
}