<?php
// Iniciar sessão para mensagens
session_start();

// Gerar token CSRF se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Limpar mensagens após exibir
$mensagem_sucesso = isset($_SESSION['sucesso']) ? $_SESSION['sucesso'] : null;
$mensagem_erro = isset($_SESSION['erro']) ? $_SESSION['erro'] : null;
unset($_SESSION['sucesso']);
unset($_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ink Agenda Pro - Agendamento de Tatuagens</title>
    <link rel="stylesheet" href="css/estile.css">
</head>
<body>
    
    <!-- Header -->
    <header>
        
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">I</div>
                    <div class="logo-text">Ink Agenda Pro</div>
                </div>
                <nav>
                    <a href="#" onclick="showSection('home')" class="nav-link active">Início</a>
                    <a href="#" onclick="showSection('booking')" class="nav-link">Agendamento</a>
                    <a href="#" onclick="showSection('artists')" class="nav-link">Profissionais</a>
                    <a href="#" onclick="showSection('portfolio')" class="nav-link">Portfólio</a>
                    <a href="#" onclick="showSection('calendar')" class="nav-link">Calendário</a>
                    <a href="#" onclick="showSection('contact')" class="nav-link">Contato</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Home Section -->
    <section id="home" class="section">
        <div class="hero">
            <div class="container">
                <h1>Agende sua Tatuagem</h1>
                <p>Conecte-se com os melhores profissionais de tatuagem e agende sua próxima arte corporal com facilidade e segurança.</p>
                <div class="cta-buttons">
                    <button class="btn btn-primary" onclick="showSection('booking')">Agendar Agora</button>
                    <button class="btn btn-secondary" onclick="showSection('portfolio')">Ver Portfólio</button>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="container">
            <h2 class="section-title">Por que escolher nossa plataforma?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📅</div>
                    <h3>Agendamento Fácil</h3>
                    <p>Sistema intuitivo para agendar sua tatuagem com os melhores profissionais da região.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3>Portfólio Completo</h3>
                    <p>Explore trabalhos dos nossos profissionais e encontre o estilo perfeito para sua tatuagem.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⭐</div>
                    <h3>Profissionais Qualificados</h3>
                    <p>Apenas tatuadores experientes e certificados fazem parte da nossa rede de profissionais.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="booking" class="section hidden">
        <div class="container">
            <h2 class="section-title">Agendar Tatuagem</h2>
            
            <?php if ($mensagem_sucesso): ?>
            <div class="success-message show">
                ✅ <?php echo htmlspecialchars($mensagem_sucesso); ?>
            </div>
            <?php endif; ?>

            <?php if ($mensagem_erro): ?>
            <div class="error-message show">
                ❌ <?php echo htmlspecialchars($mensagem_erro); ?>
            </div>
            <?php endif; ?>
            
            <form class="booking-form" method="POST" action="processar_agendamento.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nome Completo</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="artist">Profissional</label>
                        <select id="artist" name="artist" required>
                            <option value="">Selecione um profissional</option>
                            <option value="ilidio-soares">Ilidio Soares - Blackwork</option>
                            <option value="carlos-santos">Carlos Santos - Tradicional</option>
                            <option value="maria-costa">Maria Costa - Minimalista</option>
                            <option value="joao-oliveira">João Oliveira - Blackwork</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Data Preferida</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Horário Preferido</label>
                        <select id="time" name="time" required>
                            <option value="">Selecione um horário</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="style">Estilo da Tatuagem</label>
                    <select id="style" name="style" required>
                        <option value="">Selecione o estilo</option>
                        <option value="realismo">Realismo</option>
                        <option value="tradicional">Tradicional</option>
                        <option value="minimalista">Minimalista</option>
                        <option value="blackwork">Blackwork</option>
                        <option value="colorida">Colorida</option>
                        <option value="geometrica">Geométrica</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Descrição da Tatuagem</label>
                    <textarea id="description" name="description" rows="4" placeholder="Descreva o que você gostaria de tatuar, tamanho, localização no corpo, etc."></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-full-width">Agendar Tatuagem</button>
            </form>
        </div>
    </section>

    <!-- Artists Section -->
    <section id="artists" class="section hidden">
        <div class="container">
            <h2 class="section-title">Nossos Profissionais</h2>
            <div class="artists-grid">
                <div class="artist-card">
                    <div class="artist-image">
                        <img src="img/ilidio-perfil.jpeg" alt="Ilidio soares - Blackwork" loading="lazy">
                    </div>
                    <div class="artist-info">
                        <h3 class="artist-name">Ilidio Soares</h3>
                        <p class="artist-specialty">Especialista em Blackwork, Fineline, Florais, Preto e cinza</p>
                        <div class="artist-rating">
                            <span class="stars">★★★★★</span>
                            <span>4.9 (127 avaliações)</span>
                        </div>
                        <p>8 anos de experiência em tatuagens traços delicados.</p>
                        <button class="btn btn-primary btn-artist" onclick="bookArtist('ilidio soares')">Agendar com Ilidio</button>
                    </div>
                </div>

                <div class="artist-card">
                    <div class="artist-image">
                        <img src="" alt="Carlos Santos - Tradicional" loading="lazy">
                    </div>
                    <div class="artist-info">
                        <h3 class="artist-name">Nathana Soares</h3>
                        <p class="artist-specialty">Especialista em Tradicional</p>
                        <div class="artist-rating">
                            <span class="stars">★★★★★</span>
                            <span>4.8 (95 avaliações)</span>
                        </div>
                        <p>8 anos de experiência em tatuagens tradicionais americanas.</p>
                        <button class="btn btn-primary btn-artist" onclick="bookArtist('carlos-santos')">Agendar com Nathana Soares</button>
                    </div>
                </div>

                <div class="artist-card">
                    <div class="artist-image">
                        <img src="https://via.placeholder.com/400x300/10B981/FFFFFF?text=Maria+Costa" alt="Maria Costa - Minimalista" loading="lazy">
                    </div>
                    <div class="artist-info">
                        <h3 class="artist-name">Maria Costa</h3>
                        <p class="artist-specialty">Especialista em Minimalista</p>
                        <div class="artist-rating">
                            <span class="stars">★★★★★</span>
                            <span>4.7 (82 avaliações)</span>
                        </div>
                        <p>3 anos de experiência em tatuagens minimalistas e delicadas.</p>
                        <button class="btn btn-primary btn-artist" onclick="bookArtist('maria-costa')">Agendar com Maria</button>
                    </div>
                </div>

                <div class="artist-card">
                    <div class="artist-image">
                        <img src="https://via.placeholder.com/400x300/7C3AED/FFFFFF?text=João+Oliveira" alt="João Oliveira - Blackwork" loading="lazy">
                    </div>
                    <div class="artist-info">
                        <h3 class="artist-name">João Oliveira</h3>
                        <p class="artist-specialty">Especialista em Blackwork</p>
                        <div class="artist-rating">
                            <span class="stars">★★★★★</span>
                            <span>4.9 (110 avaliações)</span>
                        </div>
                        <p>6 anos de experiência em blackwork e tatuagens geométricas.</p>
                        <button class="btn btn-primary btn-artist" onclick="bookArtist('joao-oliveira')">Agendar com João</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="section hidden">
        <div class="container">
            <h2 class="section-title">Portfólio</h2>
            
            <!-- Filtro por Artista -->
            <div class="portfolio-filters">
                <button class="filter-btn active" onclick="filterPortfolio('all')">Todos os Artistas</button>
                <button class="filter-btn" onclick="filterPortfolio('ilidio-soares')">Ilidio Soares</button>
                <button class="filter-btn" onclick="filterPortfolio('carlos-santos')">Carlos Santos</button>
                <button class="filter-btn" onclick="filterPortfolio('maria-costa')">Maria Costa</button>
                <button class="filter-btn" onclick="filterPortfolio('joao-oliveira')">João Oliveira</button>
            </div>

            <div class="portfolio-grid">
                <!-- Ilidio Soares - Blackwork -->
                <div class="portfolio-item" data-artist="ilidio-soares" onclick="openImageModal('img/tattoo-floral.jpg')">
                    <img src="img/tattoo-floral.jpg" alt="Blackwork - Mulher com Águia" loading="lazy">
                    <div class="portfolio-artist-tag">Ilidio Soares</div>
                </div>
                <div class="portfolio-item" data-artist="ilidio-soares" onclick="openImageModal('https://via.placeholder.com/300x300/000000/FFFFFF?text=Mandala+Blackwork')">
                    <img src="img/tattoo-floral.jpg" alt="Blackwork - Mandala" loading="lazy">
                    <div class="portfolio-artist-tag">Ilidio Soares</div>
                </div>

                <!-- Carlos Santos - Tradicional -->
                <div class="portfolio-item" data-artist="carlos-santos" onclick="openImageModal('https://via.placeholder.com/300x300/3B82F6/FFFFFF?text=Águia+Tradicional')">
                    <img src="https://via.placeholder.com/300x300/3B82F6/FFFFFF?text=Águia+Tradicional" alt="Tradicional - Águia" loading="lazy">
                    <div class="portfolio-artist-tag">Carlos Santos</div>
                </div>
                <div class="portfolio-item" data-artist="carlos-santos" onclick="openImageModal('https://via.placeholder.com/300x300/9C27B0/FFFFFF?text=Caveira+Tradicional')">
                    <img src="https://via.placeholder.com/300x300/9C27B0/FFFFFF?text=Caveira+Tradicional" alt="Tradicional - Caveira" loading="lazy">
                    <div class="portfolio-artist-tag">Carlos Santos</div>
                </div>

                <!-- Maria Costa - Minimalista -->
                <div class="portfolio-item" data-artist="maria-costa" onclick="openImageModal('https://via.placeholder.com/300x300/10B981/FFFFFF?text=Lua+Minimalista')">
                    <img src="https://via.placeholder.com/300x300/10B981/FFFFFF?text=Lua+Minimalista" alt="Minimalista - Lua" loading="lazy">
                    <div class="portfolio-artist-tag">Maria Costa</div>
                </div>
                <div class="portfolio-item" data-artist="maria-costa" onclick="openImageModal('https://via.placeholder.com/300x300/FF5722/FFFFFF?text=Coração+Minimalista')">
                    <img src="https://via.placeholder.com/300x300/FF5722/FFFFFF?text=Coração+Minimalista" alt="Minimalista - Coração" loading="lazy">
                    <div class="portfolio-artist-tag">Maria Costa</div>
                </div>
                <div class="portfolio-item" data-artist="maria-costa" onclick="openImageModal('https://via.placeholder.com/300x300/FF6B6B/FFFFFF?text=Borboleta+Colorida')">
                    <img src="https://via.placeholder.com/300x300/FF6B6B/FFFFFF?text=Borboleta+Colorida" alt="Colorida - Borboleta" loading="lazy">
                    <div class="portfolio-artist-tag">Maria Costa</div>
                </div>

                <!-- João Oliveira -->
                <div class="portfolio-item" data-artist="joao-oliveira" onclick="openImageModal('https://via.placeholder.com/300x300/4ECDC4/FFFFFF?text=Triângulos+Geométricos')">
                    <img src="https://via.placeholder.com/300x300/4ECDC4/FFFFFF?text=Triângulos+Geométricos" alt="Geométrica - Triângulos" loading="lazy">
                    <div class="portfolio-artist-tag">João Oliveira</div>
                </div>
                <div class="portfolio-item" data-artist="joao-oliveira" onclick="openImageModal('https://via.placeholder.com/300x300/E91E63/FFFFFF?text=Rosa+Realismo')">
                    <img src="https://via.placeholder.com/300x300/E91E63/FFFFFF?text=Rosa+Realismo" alt="Realismo - Rosa" loading="lazy">
                    <div class="portfolio-artist-tag">João Oliveira</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section hidden">
        <div class="container">
            <h2 class="section-title">Contato</h2>
            <form class="booking-form" method="POST" action="processar_contato.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="contactName">Nome</label>
                    <input type="text" id="contactName" name="contactName" required>
                </div>
                <div class="form-group">
                    <label for="contactEmail">E-mail</label>
                    <input type="email" id="contactEmail" name="contactEmail" required>
                </div>
                <div class="form-group">
                    <label for="contactSubject">Assunto</label>
                    <input type="text" id="contactSubject" name="contactSubject" required>
                </div>
                <div class="form-group">
                    <label for="contactMessage">Mensagem</label>
                    <textarea id="contactMessage" name="contactMessage" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full-width">Enviar Mensagem</button>
            </form>
        </div>
    </section>

    <!-- Calendar Section -->
    <section id="calendar" class="section hidden">
        <div class="container">
            <h2 class="section-title">Meus Agendamentos</h2>
            <div class="calendar-controls">
                <button class="btn btn-primary" onclick="authenticateGoogle()" id="authButton">Conectar Google Calendar</button>
                <button class="btn btn-secondary" onclick="loadCalendarEvents()" id="loadButton" class="hidden">Atualizar Agendamentos</button>
            </div>
            <div class="calendar-status" id="calendarStatus">
                <p>🔄 Conecte sua conta Google para visualizar seus agendamentos.</p>
            </div>
            <div class="calendar-events" id="calendarEvents">
                <!-- Events will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Modal Lightbox -->
    <div class="modal" id="portfolioModal">
        <div class="modal-content image-modal">
            <button class="close-btn" onclick="closeModal()">&times;</button>
            <img id="modalImage" src="" alt="Imagem em tamanho total" loading="lazy">
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <div class="logo-icon">I</div>
                    <span>© 2024 Ink Agenda Pro. Todos os direitos reservados.</span>
                </div>
                <div class="footer-links">
                    <a href="https://instagram.com" target="_blank" rel="noopener">Instagram</a>
                    <a href="https://facebook.com" target="_blank" rel="noopener">Facebook</a>
                    <a href="https://wa.me" target="_blank" rel="noopener">WhatsApp</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script src="https://apis.google.com/js/api.js" onload="initGoogleAPI()"></script>
</body>
</html>
