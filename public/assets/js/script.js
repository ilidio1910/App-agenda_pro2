/**
 * Ink Agenda Pro - Script Principal
 * Funcionalidades da página principal
 */

// Mostrar/ocultar seções
function showSection(sectionId) {
    // Ocultar todas as seções
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });

    // Remover classe active de todos os links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });

    // Mostrar seção selecionada
    const section = document.getElementById(sectionId);
    if (section) {
        section.classList.add('active');
    }

    // Adicionar classe active ao link correspondente
    event.target.classList.add('active');

    // Scroll para o topo
    window.scrollTo(0, 0);
}

// Abrir modal de portfolio
function openModal(title) {
    const modal = document.getElementById('portfolioModal');
    if (modal) {
        document.getElementById('modalTitle').textContent = title;
        modal.classList.add('active');
    }
}

// Fechar modal
function closeModal() {
    const modal = document.getElementById('portfolioModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('portfolioModal');
    if (event.target === modal) {
        modal.classList.remove('active');
    }
};

// Agendar com artista específico
function bookArtist(artistName) {
    showSection('booking');
    const artistSelect = document.getElementById('artist');
    if (artistSelect) {
        artistSelect.value = artistName.toLowerCase().replace(/\s+/g, '-');
    }
}

// Inicializar com seção Home ativa
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar seção home por padrão
    const homeSection = document.getElementById('home');
    if (homeSection) {
        homeSection.classList.add('active');
    }

    // Ativar primeiro link da navegação
    const firstNavLink = document.querySelector('.nav-link');
    if (firstNavLink) {
        firstNavLink.classList.add('active');
    }

    // Setup de navegação
    setupNavigation();

    // Validação de formulários
    setupFormValidation();
});

// Setup de navegação
function setupNavigation() {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('onclick')
                .match(/'([^']+)'/)[1];
            showSection(sectionId);
        });
    });
}

// Validação de formulários
function setupFormValidation() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[required], textarea[required], select[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#EF4444';
                } else {
                    input.style.borderColor = '#E5E7EB';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios!');
            }
        });

        // Remover erro ao focar no input
        form.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#E5E7EB';
            });
        });
    });
}

// Formatação de telefone
function formatPhone(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 0) {
        if (value.length <= 2) {
            value = `(${value}`;
        } else if (value.length <= 7) {
            value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
        } else {
            value = `(${value.slice(0, 2)}) ${value.slice(2, 7)}-${value.slice(7, 11)}`;
        }
    }
    input.value = value;
}

// Exportar para CSV
function exportarCSV(tipo) {
    const table = document.querySelector('table');
    if (!table) return;

    let csv = [];
    table.querySelectorAll('th').forEach(th => {
        csv.push(th.textContent);
    });
    csv = [csv.join(',')];

    table.querySelectorAll('tbody tr').forEach(tr => {
        let row = [];
        tr.querySelectorAll('td').forEach(td => {
            row.push('"' + td.textContent.replace(/"/g, '""') + '"');
        });
        csv.push(row.join(','));
    });

    const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
    const link = document.createElement('a');
    link.setAttribute('href', encodeURI(csvContent));
    link.setAttribute('download', tipo + '_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}

// Temas e preferências do usuário
function saveThemePreference(theme) {
    localStorage.setItem('theme', theme);
}

function getThemePreference() {
    return localStorage.getItem('theme') || 'light';
}

// Notificações toast simples
function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, duration);
}

// Logging para desenvolvimento
console.log('🎨 Ink Agenda Pro - Script carregado com sucesso!');
