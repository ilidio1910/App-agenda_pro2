// Função para exibir/ocultar seções
function showSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    const navLinks = document.querySelectorAll('.nav-link');

    // Remover classe active de todos
    sections.forEach(section => section.classList.add('hidden'));
    navLinks.forEach(link => link.classList.remove('active'));

    // Adicionar classe active ao selecionado
    document.getElementById(sectionId)?.classList.remove('hidden');
    event.target?.classList.add('active');
}

// Função para abrir modal
function openModal(title) {
    const modal = document.getElementById('portfolioModal');
    const modalTitle = document.getElementById('modalTitle');

    modalTitle.textContent = title;
    modal.classList.add('active');
}

// Função para fechar modal
function closeModal() {
    const modal = document.getElementById('portfolioModal');
    modal.classList.remove('active');
}

// Função para agendar com um artista
function bookArtist(artistName) {
    document.getElementById('artist').value = artistName;
    showSection('booking');
}

// Fechar modal ao clicar fora dele
document.addEventListener('click', function(event) {
    const modal = document.getElementById('portfolioModal');
    if (event.target === modal) {
        closeModal();
    }
});

// Função placeholder para Google API
function authenticateGoogle() {
    alert('Integração com Google Calendar será configurada em breve');
}

function loadCalendarEvents() {
    alert('Eventos serão carregados do Google Calendar');
}

function initGoogleAPI() {
    // API do Google será configurada aqui
}

// Remover mensagens após 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('.success-message.show');
    const errorMessage = document.querySelector('.error-message.show');

    if (successMessage) {
        setTimeout(() => {
            successMessage.classList.remove('show');
        }, 5000);
    }

    if (errorMessage) {
        setTimeout(() => {
            errorMessage.classList.remove('show');
        }, 5000);
    }
});
