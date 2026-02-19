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

// Função para abrir imagem em modal
function openImageModal(imageSrc) {
    console.log('Abrindo imagem:', imageSrc);
    
    const modal = document.getElementById('portfolioModal');
    const modalImage = document.getElementById('modalImage');

    if (modal && modalImage) {
        modalImage.src = imageSrc;
        modal.classList.add('active');
        console.log('Modal aberto com sucesso');
    } else {
        console.error('Modal ou imagem não encontrados');
    }
}

// Função para fechar modal
function closeModal() {
    const modal = document.getElementById('portfolioModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Fechar modal ao clicar no background (fora da imagem)
document.addEventListener('click', function(event) {
    const modal = document.getElementById('portfolioModal');
    const modalContent = modal?.querySelector('.modal-content');
    
    // Fechar apenas se clicar no fundo (modal), não no conteúdo
    if (modal && event.target === modal) {
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

// Função para filtrar portfólio por artista
function filterPortfolio(artistId) {
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    const filterButtons = document.querySelectorAll('.filter-btn');

    // Atualizar botões de filtro
    filterButtons.forEach(btn => {
        btn.classList.remove('active');
        if ((artistId === 'all' && btn.textContent === 'Todos os Artistas') ||
            (btn.textContent.toLowerCase().includes(artistId))) {
            btn.classList.add('active');
        }
    });

    // Filtrar itens
    portfolioItems.forEach(item => {
        if (artistId === 'all') {
            item.classList.remove('hidden');
        } else {
            if (item.dataset.artist === artistId) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        }
    });
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
