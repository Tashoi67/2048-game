// ===== Игра 2048 =====
// Чистый JavaScript без библиотек.
// Логика: поле 4x4, стрелки двигают плитки, одинаковые соединяются.
// После конца игры результат отправляется на сервер (Laravel сохраняет в БД).

const Game = {
    // Настройки, которые приходят со страницы (адрес сохранения, CSRF-токен)
    saveUrl: '',
    csrfToken: '',

    size: 4,            // размер поля 4x4
    grid: [],           // двумерный массив с числами (0 = пустая клетка)
    score: 0,           // текущие очки
    over: false,        // игра закончена?
    saved: false,       // результат уже отправлен на сервер?

    // Инициализация — вызывается со страницы игры
    init(config) {
        this.saveUrl = config.saveUrl;
        this.csrfToken = config.csrfToken;

        // Управление с клавиатуры
        document.addEventListener('keydown', (e) => {
            const keys = { ArrowLeft: 'left', ArrowRight: 'right', ArrowUp: 'up', ArrowDown: 'down' };
            if (keys[e.key]) {
                e.preventDefault(); // чтобы страница не листалась
                this.move(keys[e.key]);
            }
        });

        // Управление свайпами (для телефона)
        const board = document.getElementById('board');
        let startX = 0, startY = 0;

        board.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });

        board.addEventListener('touchend', (e) => {
            const dx = e.changedTouches[0].clientX - startX;
            const dy = e.changedTouches[0].clientY - startY;

            if (Math.abs(dx) < 30 && Math.abs(dy) < 30) return; // слишком короткий свайп

            if (Math.abs(dx) > Math.abs(dy)) {
                this.move(dx > 0 ? 'right' : 'left');
            } else {
                this.move(dy > 0 ? 'down' : 'up');
            }
        });

        this.restart();
    },

    // Начать заново
    restart() {
        this.grid = Array.from({ length: this.size }, () => Array(this.size).fill(0));
        this.score = 0;
        this.over = false;
        this.saved = false;
        this.addRandomTile();
        this.addRandomTile();
        this.hideOverlay();
        this.render();
    },

    // Поставить новую плитку (2 или 4) в случайную пустую клетку
    addRandomTile() {
        const empty = [];
        for (let r = 0; r < this.size; r++) {
            for (let c = 0; c < this.size; c++) {
                if (this.grid[r][c] === 0) empty.push([r, c]);
            }
        }
        if (empty.length === 0) return;

        const [row, col] = empty[Math.floor(Math.random() * empty.length)];
        this.grid[row][col] = Math.random() < 0.9 ? 2 : 4; // 2 появляется в 90% случаев
    },

    // Главная логика: сдвиг всех плиток в одну сторону
    move(direction) {
        if (this.over) return;

        const before = JSON.stringify(this.grid); // запомним поле до хода

        // Для простоты всегда "сдвигаем влево", а поле поворачиваем
        const rotated = this.rotateForDirection(this.grid, direction);

        for (let r = 0; r < this.size; r++) {
            rotated[r] = this.mergeLine(rotated[r]); // сжимаем каждую строку
        }

        this.grid = this.rotateBack(rotated, direction);

        // Если после хода поле не изменилось — ход "пустой", ничего не делаем
        if (JSON.stringify(this.grid) === before) return;

        this.addRandomTile();
        this.render();

        // Проверяем конец игры
        if (this.hasMoveAvailable()) {
            // Ходы ещё есть
        } else {
            this.endGame(false); // ходов нет — проигрыш
        }
    },

    // Сжать одну строку: убрать нули, соединить одинаковые соседние плитки
    mergeLine(line) {
        // 1. Убираем пустые клетки
        const tiles = line.filter((v) => v !== 0);

        // 2. Соединяем одинаковые соседние
        const merged = [];
        for (let i = 0; i < tiles.length; i++) {
            if (tiles[i] === tiles[i + 1]) {
                merged.push(tiles[i] * 2);
                this.score += tiles[i] * 2;  // за соединение даём очки
                i++;                          // пропускаем вторую плитку
            } else {
                merged.push(tiles[i]);
            }
        }

        // 3. Дополняем нулями справа
        while (merged.length < this.size) merged.push(0);

        return merged;
    },

    // Поворачиваем поле так, чтобы направление хода стало "влево"
    rotateForDirection(grid, direction) {
        if (direction === 'left') return grid.map((row) => [...row]);
        if (direction === 'right') return grid.map((row) => [...row].reverse());
        if (direction === 'up') return this.transpose(grid);
        return this.transpose(grid).map((row) => [...row].reverse()); // down
    },

    // Обратный поворот поля после хода
    rotateBack(grid, direction) {
        if (direction === 'left') return grid;
        if (direction === 'right') return grid.map((row) => [...row].reverse());
        if (direction === 'up') return this.transpose(grid);
        return this.transpose(grid.map((row) => [...row].reverse())); // down
    },

    // Транспонирование матрицы (строки становятся столбцами)
    transpose(grid) {
        return grid[0].map((_, c) => grid.map((row) => row[c]));
    },

    // Есть ли ещё возможные ходы?
    hasMoveAvailable() {
        for (let r = 0; r < this.size; r++) {
            for (let c = 0; c < this.size; c++) {
                // Есть пустая клетка — ход есть
                if (this.grid[r][c] === 0) return true;
                // Есть одинаковый сосед справа или снизу — ход есть
                if (c + 1 < this.size && this.grid[r][c] === this.grid[r][c + 1]) return true;
                if (r + 1 < this.size && this.grid[r][c] === this.grid[r + 1][c]) return true;
            }
        }
        return false;
    },

    // Конец игры
    endGame(won) {
        this.over = true;
        const maxTile = Math.max(...this.grid.flat());
        this.showOverlay(
            won ? 'Победа!' : 'Игра окончена!',
            `Очки: ${this.score} · Максимальная плитка: ${maxTile}`
        );
        this.saveResult(maxTile);
    },

    // Отправить результат на сервер (Laravel сохранит в таблицу scores)
    async saveResult(maxTile) {
        if (this.saved) return;
        this.saved = true;

        try {
            const response = await fetch(this.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({ points: this.score, best_tile: maxTile }),
            });
            const data = await response.json();
            console.log('Результат сохранён, лучший рекорд:', data.best);
        } catch (error) {
            console.error('Не удалось сохранить результат:', error);
        }
    },

    // ===== Отрисовка =====

    // Нарисовать поле заново
    render() {
        const board = document.getElementById('board');
        board.innerHTML = ''; // очищаем поле

        // Создаём 16 клеток
        for (let r = 0; r < this.size; r++) {
            for (let c = 0; c < this.size; c++) {
                const cell = document.createElement('div');
                cell.className = 'cell';

                const value = this.grid[r][c];
                if (value !== 0) {
                    const tile = document.createElement('div');
                    tile.className = 'tile';
                    tile.dataset.value = value > 2048 ? 2048 : value;
                    tile.textContent = value;
                    cell.appendChild(tile);
                }

                board.appendChild(cell);
            }
        }

        // Обновляем счёт
        document.getElementById('score').textContent = this.score;
    },

    showOverlay(title, text) {
        document.getElementById('overlay-title').textContent = title;
        document.getElementById('overlay-text').textContent = text;
        document.getElementById('overlay').classList.remove('hidden');
    },

    hideOverlay() {
        document.getElementById('overlay').classList.add('hidden');
    },
};
