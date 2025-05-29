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
}
