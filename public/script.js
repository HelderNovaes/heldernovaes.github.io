function enviarReserva() {
    // Capturar valores do formulário
    const data = document.getElementById('data').value;
    const hora = document.getElementById('hora').value;
    const duracion = document.getElementById('duracao').value;
    const cliente = document.getElementById('cliente').value;
    const valor = document.getElementById('valorTotal').value;

    // Validar que todos os campos estão preenchidos
    if (!data || !hora || !duracion || !cliente || !valor) {
        alert('¡Rellena todos los campos!');
        return;
    }

    // Criar FormData para envio
    const formData = new FormData();
    formData.append("cancha", campoSeleccionado); // variável global, deve estar definida
    formData.append("data", data);
    formData.append("hora", hora);
    formData.append("duracion", duracion);  // aqui ajustei "duracao" para "duracion" para consistência com o form
    formData.append("cliente", cliente);
    formData.append("valor", valor);

    // Enviar os dados via POST para o PHP
    fetch("reserva.php", {
        method: "POST",
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na resposta do servidor');
        }
        return response.text();
    })
    .then(resultado => {
        alert("✅ " + resultado);

        // Montar mensagem para WhatsApp
        const numeroWhatsApp = "59167728519";
        const mensaje = 
`Reserva de Cancha: ${campoSeleccionado}
Fecha: ${data}
Hora: ${hora}
Duración: ${duracion} horas
Cliente: ${cliente}
Valor Total: ${valor} Bs

¡Gracias por tu reserva!`;

        // Abrir WhatsApp numa nova aba com a mensagem preenchida
        const urlWhatsApp = `https://wa.me/${numeroWhatsApp}?text=${encodeURIComponent(mensaje)}`;
        window.open(urlWhatsApp, '_blank');

        // Fechar formulário após envio
        fecharFormulario();
    })
    .catch(error => {
        alert("❌ Erro ao registrar reserva.");
        console.error('Erro no fetch:', error);
    });
}
