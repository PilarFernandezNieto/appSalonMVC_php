let paso = 1; // Inicializamos paso a 1 para que al iniciar la app, la función mostrarSeccion cargue la sección con el paso 1 ( Servicios )
let pasoInicial = 1;
let pasoFinal = 3;

const cita = {
  id: "",
  nombre: "",
  fecha: "",
  hora: "",
  servicios: [],
};

document.addEventListener("DOMContentLoaded", function () {
  iniciarApp();
});

function iniciarApp() {
  mostrarSeccion();
  tabs(); // Cambia la seccion cuando se presionen los botones
  botonesPaginador();
  paginaSiguiente();
  paginaAnterior();

  consultarAPI();
  idCliente();
  nombreCliente(); // Añade el nombre del cliente al objeto cita
  seleccionarFecha(); // Añade la fecha
  seleccionaHora(); // Añade la hora al objeto
  mostrarResumen(); // Muestra el resumen de la cita antes de enviar
}
function mostrarSeccion() {
  // ocultar la seccion que tenga la clase de mostrar
  const seccionAnterior = document.querySelector(".mostrar");
  if (seccionAnterior) {
    seccionAnterior.classList.remove("mostrar");
  }

  // seleccionar la seccion por el paso
  const seccion = document.querySelector(`#paso-${paso}`);
  seccion.classList.add("mostrar");

  // Quita la clase de actual al tab anterior
  const tabAnterior = document.querySelector(".actual");
  if (tabAnterior) {
    tabAnterior.classList.remove("actual");
  }

  // Resalta el tab actual
  const tab = document.querySelector(`[data-paso="${paso}"]`);
  tab.classList.add("actual");
}

function tabs() {
  const botones = document.querySelectorAll(".tabs button");

  botones.forEach((boton) => {
    boton.addEventListener("click", (e) => {
      paso = parseInt(e.target.dataset.paso);

      mostrarSeccion();
      botonesPaginador();
    });
  });
}

function botonesPaginador() {
  const paginaSiguiente = document.querySelector("#siguiente");
  const paginaAnterior = document.querySelector("#anterior");

  if (paso === 1) {
    paginaAnterior.classList.add("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  } else if (paso === 3) {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.add("ocultar");
    mostrarResumen();
  } else {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  }
  mostrarSeccion();
}

function paginaSiguiente() {
  const paginaSiguiente = document.querySelector("#siguiente");
  paginaSiguiente.addEventListener("click", () => {
    if (paso >= pasoFinal) return;
    paso++;
    botonesPaginador();
  });
}
function paginaAnterior() {
  const paginaAnterior = document.querySelector("#anterior");
  paginaAnterior.addEventListener("click", () => {
    if (paso <= pasoInicial) return;
    paso--;
    botonesPaginador();
  });
}

async function consultarAPI() {
  try {
    //const url = `${location.origin}/api/servicios`;
    const url = '/api/servicios';
    const resultado = await fetch(url);
    const servicios = await resultado.json();
    mostrarServicios(servicios);
  } catch (error) {
    console.log(error);
  }
}

function mostrarServicios(servicios) {
  servicios.forEach((servicio) => {
    const { id, nombre, precio } = servicio;

    const nombreServicio = document.createElement("p");
    nombreServicio.classList.add("nombre-servicio");
    nombreServicio.textContent = nombre;

    const precioServicio = document.createElement("p");
    precioServicio.classList.add("precio-servicio");
    precioServicio.textContent = `$${precio}`;

    const servicioDiv = document.createElement("div");
    servicioDiv.classList.add("servicio");
    servicioDiv.dataset.idServicio = id;
    servicioDiv.onclick = function () {
      seleccionarServicio(servicio);
    };

    servicioDiv.appendChild(nombreServicio);
    servicioDiv.appendChild(precioServicio);

    document.querySelector("#servicios").appendChild(servicioDiv);
  });
}

function seleccionarServicio(servicio) {
  const { id } = servicio;
  const { servicios } = cita;

  // Comprobar si un servicio ya fue añadido
  if (servicios.some((agregado) => agregado.id === id)) {
    // Eliminarlo del arreglo servicios
    cita.servicios = servicios.filter((agregado) => agregado.id !== id);
  } else {
    // Agregarlo
    cita.servicios = [...servicios, servicio];
  }

  const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);
  divServicio.classList.toggle("seleccionado");
}

function idCliente() {
  const id = document.querySelector("#id").value;
  cita.id = id;
}

function nombreCliente() {
  const nombre = document.querySelector("#nombre").value;
  cita.nombre = nombre;
}

function seleccionarFecha() {
  const inputFecha = document.querySelector("#fecha");
  inputFecha.addEventListener("input", (e) => {
    const dia = new Date(e.target.value).getUTCDay();
    if ([6, 0].includes(dia)) {
      e.target.value = "";
      mostrarAlerta("Fines de semana no permitidos", "error", ".formulario");
    } else {
      cita.fecha = e.target.value;
    }
  });
}

function seleccionaHora() {
  const inputHora = document.querySelector("#hora");
  inputHora.addEventListener("input", (e) => {
    const horaCita = e.target.value;
    const hora = horaCita.split(":")[0];
    if (hora < 10 || hora > 18) {
      e.target.value = "";
      mostrarAlerta("Hora no válida", "error", ".formulario");
    } else {
      cita.hora = e.target.value;
    }
  });
}
function mostrarResumen() {
  const resumen = document.querySelector(".contenido-resumen");

  // Limpiar el contenido de resumen
  while (resumen.firstChild) {
    resumen.removeChild(resumen.firstChild);
  }

  if (Object.values(cita).includes("") || cita.servicios.length === 0) {
    mostrarAlerta(
      "Faltan dantos de Servicios, Fecha u Hora",
      "error",
      ".contenido-resumen",
      false
    );
    return;
  }
  // Formatear el div de resumen
  const { nombre, fecha, hora, servicios } = cita;

  // Heading para Servicios en resumen
  const headingServicios = document.createElement("h3");
  headingServicios.textContent = "Resumen de Servicios";
  resumen.appendChild(headingServicios);

  // Iterando y mostrando los servicios
  servicios.forEach((servicio) => {
    const { id, precio, nombre } = servicio;

    const contenedorServicio = document.createElement("div");
    contenedorServicio.classList.add("contenedor-servicio");

    const textoServicio = document.createElement("p");
    textoServicio.textContent = nombre;

    const precioServicio = document.createElement("p");
    precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

    contenedorServicio.appendChild(textoServicio);
    contenedorServicio.appendChild(precioServicio);
    resumen.appendChild(contenedorServicio);
  });

  // Heading para Cita en resumen
  const headingCita = document.createElement("h3");
  headingCita.textContent = "Resumen de cita";
  resumen.appendChild(headingCita);

  const nombreCliente = document.createElement("p");
  nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

  // Formatear la fecha
  const fechaObj = new Date(fecha);
  const mes = fechaObj.getMonth();
  const dia = fechaObj.getDate();
  const anio = fechaObj.getFullYear();

  const fechaUTC = new Date(Date.UTC(anio, mes, dia));
  const opciones = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  const fechaFormateada = fechaUTC.toLocaleDateString("es-ES", opciones);

  const fechaCita = document.createElement("p");
  fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

  const horaCita = document.createElement("p");
  horaCita.innerHTML = `<span>Hora:</span> ${hora} horas`;

  // Boton para crear una cita
  const botonReservar = document.createElement("button");
  botonReservar.classList.add("boton");
  botonReservar.textContent = "Reservar Cita";
  botonReservar.onclick = reservarCita;

  resumen.appendChild(nombreCliente);
  resumen.appendChild(fechaCita);
  resumen.appendChild(horaCita);

  resumen.appendChild(botonReservar);
}

async function reservarCita() {
  const { nombre, fecha, hora, servicios, id } = cita;
  const idServicios = servicios.map((servicio) => servicio.id);

  const datos = new FormData();
  datos.append("fecha", fecha);
  datos.append("hora", hora);
  datos.append("idUsuario", id);
  datos.append("servicios", idServicios);

  // Petición a la api
  try {
    const url = "/api/citas";
    const respuesta = await fetch(url, {
      method: "POST",
      body: datos,
    });
    const resultado = await respuesta.json();
    if (resultado.resultado) {
      Swal.fire({
        icon: "success",
        title: "Cita creada",
        text: "Tu cita se ha creado con éxito",
        confirmButtonText: "OK",
      }).then(() => {
        setTimeout(() => {
          window.location.reload();
        }, 3000);
      });
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Hubo un error al guardar la cita"
    });
  }
  //console.log([...datos]);// USAMOS SPREAD OPERATOR PARA MOSTRAR DATOS EN CONSOLA
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
  // Evita que se genera más de una alerta
  const alertaPrevia = document.querySelector(".alerta");

  if (alertaPrevia) {
    alertaPrevia.remove();
  }

  // Crea la alerta
  const alerta = document.createElement("div");
  alerta.textContent = mensaje;
  alerta.classList.add("alerta");
  alerta.classList.add(tipo);
  const referencia = document.querySelector(elemento);
  referencia.appendChild(alerta);

  // elimina la alerta
  if (desaparece) {
    setTimeout(() => {
      alerta.remove();
    }, 3000);
  }
}
