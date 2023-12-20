let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();

});

function iniciarApp() {
    mostrarSeccion(); // muestra la seccion actual
    tabs(); // cambia la seccion cuando se presionan los tabs
    botonesPaginador(); // agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); // consulta la API en el backend de php

    idCliente(); // almacena el id del cliente al objeto de cita
    nombreCliente(); // almacena el nombre del cliente al objeto de cita
    selecionarFecha(); // almacena la fecha seleccionada al objeto de cita
    seleccionarHora(); // almacena la hora seleccionada al objeto de cita
    mostrarResumen(); // muestra el resumen de la cita o mensaje de error en caso de que no se haya completado
}

function mostrarSeccion() {
    // ocultar seccion anterior
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }
    

    // seleccionar la seccion con el paso
    const seccion = document.querySelector(`#paso-${paso}`);
    seccion.classList.add('mostrar');

    // Quita la clase de actual al tab anterior 
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior){
        tabAnterior.classList.remove('actual');
    }

    // Resaltar el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button'); 
    
    botones.forEach( boton => {
        boton.addEventListener('click', function(e) {
            paso = parseInt(e.target.dataset.paso); // se obtiene el paso actual
            mostrarSeccion(); // se muestra la seccion correspondiente al paso

            botonesPaginador(); // se agregan o quitan los botones del paginador

            
        });    
    });
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if(paso === 3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen(); // se muestra el resumen de la cita o mensaje de error en caso de que no se haya completado
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function() {
        if(paso <= pasoInicial) return;
        paso--;

        botonesPaginador();
    });
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {
        if(paso >= pasoFinal) return;
        paso++;

        botonesPaginador();
    });    
}

async function consultarAPI() {
    try {
        const url = `${location.origin}/api/servicios`;
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio;

        // DOM Scripting
        // Generar el nombre del servicio
        const nombreServicio = document.createElement('P');
        nombreServicio.textContent = nombre;
        nombreServicio.classList.add('nombre-servicio');

        // Generar el precio del servicio
        const precioServicio = document.createElement('P');
        precioServicio.textContent = `$ ${precio}`;
        precioServicio.classList.add('precio-servicio');

        // Generar el div contenedor del servicio
        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio);
        }

        // Inyectar precio y nombre al div del servicio
        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        // Inyectar el servicio al HTML
        document.querySelector('#servicios').appendChild(servicioDiv);
    });
}

function seleccionarServicio(servicio) {
    const {id} = servicio; 
    const {servicios} = cita;

    // identificar el elemento del servicio
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    // comprombar si el servicio ya esta en el arreglo de servicios
    if(servicios.some( servicio => servicio.id === id)){
        // eliminar el servicio del arreglo
        cita.servicios = cita.servicios.filter( servicio => servicio.id !== id);
        
    } else {
        // agregar el servicio al arreglo
        cita.servicios = [...cita.servicios, servicio];
    }
    
    divServicio.classList.toggle('seleccionado');
    
    
}

function idCliente() {
    cita.id = document.querySelector('#id').value;
}

function nombreCliente() {
    cita.nombre = document.querySelector('#nombre').value;
}

function selecionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) {
        const dia = new Date(e.target.value).getUTCDay();

        if([6, 0].includes(dia)){
            e.preventDefault();
            e.target.value = '';
            mostrarAlerta('Sabados y domingos no atendemos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
    });
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {
        const horaCita = e.target.value;
        const hora = horaCita.split(':');

        if(hora[0] < 10 || hora[0] > 18){
            e.preventDefault();
            e.target.value = '';
            mostrarAlerta('Hora no valida', 'error', '.formulario');
        } else {
            cita.hora = horaCita;
        }
    });    
}

function mostrarAlerta(mensaje, tipo, elemento, desaparecer = true) {
    // si hay una alerta previa, entonces no crear otra
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia){
        alertaPrevia.remove();
    }

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');

    if(tipo === 'error'){
        alerta.classList.add('error');
    }

    // insertar en el HTML
    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparecer){
        // eliminar la alerta despues de 3 segundos
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
    

    
}
function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    // Limpiar el contenido del resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }
    
    if(Object.values(cita).includes('') || cita.servicios.length === 0){
        mostrarAlerta('Faltan datos de servicios, fecha u hora', 'error', '.contenido-resumen', false);

        return;
    }
    
    // Formatear el div de resumen
    const {nombre, fecha, hora, servicios} = cita;

    

    // Heading para servicios en Resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios Solicitados';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    servicios.forEach(servicio => {
        const {id, nombre, precio} = servicio;

        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');     
        
        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $ ${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    })

    // Heading para datos del cliente
    const headingCliente = document.createElement('H3');
    headingCliente.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCliente);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // Formatear la fecha
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const opciones = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }
    const fechaFormateada = new Date(Date.UTC(year, mes, dia)).toLocaleDateString('es-CL', opciones);
    

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} hrs`;

    // Boton para crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReservar);
}

async function reservarCita() {

    const { fecha, hora, servicios, id} = cita;

    const idServicios = servicios.map( servicio => servicio.id);

    const datos = new FormData();
    
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    try {
       // Peticion hacia la API
        const url = `${location.origin}/api/citas`;
        const respuesta = await fetch(url, {
            method: 'POST', 
            body: datos
        });
        
        const resultado = await respuesta.json();
        

        if(resultado.resultado){
            Swal.fire({
                icon: "success",
                title: "Genial!",
                text: "Tu cita ha sido reservada correctamente",
            }).then( () => {
                setTimeout(() => {
                    window.location.reload();  
                }, 1000);
                      
            })
        } 
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error!",
            text: "algo salio mal",
        });        
    }
    
    
    //console.log([...datos]); // para ver los datos que se envian al backend
}