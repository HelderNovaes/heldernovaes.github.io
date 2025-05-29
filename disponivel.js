const todosHorarios = [
  '06:00', '06:30', '07:00', '07:30',
  '08:00', '08:30', '09:00', '09:30',
  '10:00', '10:30', '11:00', '11:30',
  '12:00', '12:30', '13:00', '13:30',
  '14:00', '14:30', '15:00', '15:30',
  '16:00', '16:30', '17:00', '17:30',
  '18:00', '18:30', '19:00', '19:30',
  '20:00', '20:30', '21:00', '21:30',
  '22:00', '22:30', '23:00', '23:30',
  '00:00',
];
function carregarHorarios() {
  const cancha = document.getElementById('cancha').value;
  const data = document.getElementById('data').value;
  const lista = document.getElementById('listaHorarios');

  if (!cancha || !data) {
    alert('Escolha a cancha e a data.');
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
            // ✅ Envia para index.html com os parâmetros
            const url = `index.html?cancha=${encodeURIComponent(cancha)}&data=${encodeURIComponent(data)}&hora=${encodeURIComponent(horario)}`;
            window.location.href = url;
          };
        }

        lista.appendChild(div);
      });
    });
}

