const todosHorarios = [];

// Gera horários de 15 em 15 minutos entre 06:00 e 23:45 + 00:00
for (let hora = 6; hora <= 23; hora++) {
  for (let min = 0; min < 60; min += 15) {
    const h = hora.toString().padStart(2, '0');
    const m = min.toString().padStart(2, '0');
    todosHorarios.push(`${h}:${m}`);
  }
}
todosHorarios.push('00:00');

function carregarHorarios() {
  const cancha = document.getElementById('cancha').value;
  const data = document.getElementById('data').value;
 const lista = document.getElementById('listaHorarios');
const resumos = document.getElementById('resumosOcupados'); // novo container para os resumos


  if (!cancha || !data) {
    alert('Elegí la cancha y la fecha.');
    return;
  }

  fetch(`disponibilidade.php?cancha=${encodeURIComponent(cancha)}&data=${encodeURIComponent(data)}`)
    .then(res => res.json())
    .then(reservas => {
      console.log("Reservas recebidas:", reservas);
      lista.innerHTML = '';
      const horariosOcupados = new Set();

      if (!Array.isArray(reservas)) {
        alert("Error: respuesta inválida del servidor.");
        return;
      }

      reservas.forEach(res => {
        if (!res.hora_inicio || !res.hora_fim) {
          console.warn("Reserva incompleta:", res);
          return;
        }

        // Cria visual de resumo do horário ocupado
       const resumo = document.createElement('p');
resumo.className = 'resumo-ocupado';

const linkTelefone = document.createElement('a');
linkTelefone.href = `tel:${res.telefono}`;
linkTelefone.textContent = res.telefono;
linkTelefone.style.color = '#007acc';
linkTelefone.style.textDecoration = 'underline';

resumo.innerHTML = `⛔ Horario ocupado de <strong>${res.hora_inicio}</strong> a <strong>${res.hora_fim}</strong> — <strong>${res.nombre}</strong>, 📞 `;
resumo.appendChild(linkTelefone);
resumo.innerHTML += `, ${res.cancha}`;

resumos.appendChild(resumo);


        // Marca os blocos ocupados de 15 em 15 minutos
        let inicio = new Date(`1970-01-01T${res.hora_inicio}:00`);
        let fim = new Date(`1970-01-01T${res.hora_fim}:00`);

        while (inicio < fim) {
          const h = inicio.getHours().toString().padStart(2, '0');
          const m = inicio.getMinutes().toString().padStart(2, '0');
          horariosOcupados.add(`${h}:${m}`);
          inicio.setMinutes(inicio.getMinutes() + 15);
        }
      });

      // Monta o grid de horários
      todosHorarios.forEach(horario => {
        const div = document.createElement('div');
        div.className = 'horario';
        div.textContent = horario;

        if (horariosOcupados.has(horario)) {
          div.classList.add('ocupado');
        } else {
          div.onclick = () => {
            const url = `index.html?cancha=${encodeURIComponent(cancha)}&data=${encodeURIComponent(data)}&hora=${encodeURIComponent(horario)}`;
            window.location.href = url;
          };
        }

        lista.appendChild(div);
      });
    })
    .catch(error => {
      console.error("Erro na requisição:", error);
      alert("Error al cargar los datos del servidor.");
    });
}
