const todosHorarios = [];

for (let hora = 6; hora <= 23; hora++) {
  for (let min = 0; min < 60; min += 15) {
    const h = hora.toString().padStart(2, '0');
    const m = min.toString().padStart(2, '0');
    todosHorarios.push(`${h}:${m}`);
  }
}
todosHorarios.push('00:00'); // adiciona meia-noite

function carregarHorarios() {
  const cancha = document.getElementById('cancha').value;
  const data = document.getElementById('data').value;
  const lista = document.getElementById('listaHorarios');

  if (!cancha || !data) {
    alert('Elegí la cancha y la fecha.');
    return;
  }

  fetch(`disponibilidade.php?cancha=${encodeURIComponent(cancha)}&data=${encodeURIComponent(data)}`)
    .then(res => res.json())
    .then(ocupados => {
      lista.innerHTML = '';

      todosHorarios.forEach(horario => {
        const div = document.createElement('div');
        div.className = 'horario';
        div.textContent = horario;

        if (ocupados.includes(horario)) {
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
    .catch(err => {
      console.error('Erro ao carregar horários:', err);
      alert('Erro ao carregar os horários disponíveis.');
    });
}
