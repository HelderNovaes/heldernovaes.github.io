function enviarReserva() {
    // Capturar valores do formulário
    const data = document.getElementById('data').value;
const hora = document.getElementById('hora').value;
const duracion = document.getElementById('duracao').value;
const cliente = document.getElementById('cliente').value;
const valor = document.getElementById('valorTotal').value;
const whatsapp = document.getElementById('whatsapp').value;
const email = document.getElementById('email').value;

    
    // Validar que todos os campos estão preenchidos
if (!data || !hora || !duracion || !cliente || !valor || !whatsapp || !email) {
    alert('¡Rellena todos los campos!');
    return;
}


    // Criar FormData para envio
    const formData = new FormData();
formData.append("cancha", campoSeleccionado); // variável global
formData.append("data", data);
formData.append("hora", hora);
formData.append("duracion", duracion);
formData.append("cliente", cliente);
formData.append("valor", valor);
formData.append("whatsapp", whatsapp);
formData.append("email", email);


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

    // Montar mensagem para WhatsApp com todos os dados
    const numeroWhatsApp = "59167728519";
    const mensaje = 
`📅 *Reserva de Cancha: ${campoSeleccionado}*
🗓️ Fecha: ${data}
🕒 Hora: ${hora}
⏳ Duración: ${duracion} horas
👤 Cliente: ${cliente}
💵 Valor Total: ${valor} Bs
📱 WhatsApp: ${whatsapp}
📧 Email: ${email}

¡Gracias por tu reserva! 🙌`;

    const urlWhatsApp = `https://wa.me/${numeroWhatsApp}?text=${encodeURIComponent(mensaje)}`;
    window.open(urlWhatsApp, "_blank");
})
.catch(error => {
    console.error("Erro ao enviar reserva:", error);
    alert("❌ Ocurrió un error al enviar la reserva.");
});
}
