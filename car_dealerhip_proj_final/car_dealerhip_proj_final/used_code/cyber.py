import pygame
import random
import sys
import json
import os

# Initialize Pygame
pygame.init()

# Screen setup
WIDTH, HEIGHT = 600, 600
GRID_SIZE = 20
screen = pygame.display.set_mode((WIDTH, HEIGHT))
pygame.display.set_caption("CyberSnake - Cybersecurity Quiz Snake")

# Colors
WHITE = (255, 255, 255)
BLACK = (0, 0, 0)
GREEN = (0, 255, 0)
RED = (255, 0, 0)
YELLOW = (255, 255, 0)

# Fonts
font = pygame.font.SysFont("Arial", 24)

# High score file
HIGHSCORE_FILE = "highscore.json"

# Load high score
if os.path.exists(HIGHSCORE_FILE):
    with open(HIGHSCORE_FILE, "r") as f:
        high_score_data = json.load(f)
        HIGH_SCORE = high_score_data.get("score", 0)
else:
    HIGH_SCORE = 0

clock = pygame.time.Clock()

# Questions (same as before)
QUESTIONS = [
    {
        "question": "What does VPN stand for?",
        "answers": ["Virtual Private Network", "Very Private Network", "Virtual Public Network"],
        "correct": 0
    },
    {
        "question": "What is a common method used to protect data?",
        "answers": ["Encryption", "Decryption", "Compression"],
        "correct": 0
    },
    {
        "question": "What type of attack involves tricking users into revealing sensitive info?",
        "answers": ["Phishing", "DDoS", "Brute Force"],
        "correct": 0
    },
    {
        "question": "What does MFA stand for?",
        "answers": ["Multi-Factor Authentication", "Multiple Form Access", "Multi-Format Authorization"],
        "correct": 0
    },
    {
        "question": "Which of these is a malware?",
        "answers": ["Virus", "Firewall", "Antivirus"],
        "correct": 0
    }
]

class Snake:
    def _init_(self):
        self.positions = [(WIDTH // 2, HEIGHT // 2)]
        self.direction = (0, -GRID_SIZE)  # start moving up
        self.length = 1

    def move(self):
        head_x, head_y = self.positions[0]
        delta_x, delta_y = self.direction
        new_head = ((head_x + delta_x)) % WIDTH, ((head_y + delta_y) % HEIGHT)
        # Self-collision
        if new_head in self.positions:
            return False
        self.positions = [new_head] + self.positions[:-1]
        return True

    def grow(self):
        self.positions = [self.positions[0]] + self.positions
    
    def set_direction(self, dir):
        # prevent reversing
        if (dir[0] * -1, dir[1] * -1) != self.direction:
            self.direction = dir

    def draw(self, surface):
        for pos in self.positions:
            pygame.draw.rect(surface, GREEN, pygame.Rect(pos[0], pos[1], GRID_SIZE, GRID_SIZE))

class Fruit:
    def _init_(self):
        self.position = self.random_position()
        self.question = random.choice(QUESTIONS)

    def random_position(self):
        while True:
            x = random.randrange(0, WIDTH, GRID_SIZE)
            y = random.randrange(0, HEIGHT, GRID_SIZE)
            # ensure no overlap
            return (x, y)

    def draw(self, surface):
        pygame.draw.rect(surface, RED, pygame.Rect(self.position[0], self.position[1], GRID_SIZE, GRID_SIZE))


def draw_text(surface, text, pos, color=WHITE):
    label = font.render(text, True, color)
    surface.blit(label, pos)

def ask_question(question_data):
    selected = 0
    while True:
        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                pygame.quit()
                sys.exit()
            elif event.type == pygame.KEYDOWN:
                if event.key == pygame.K_UP:
                    selected = (selected - 1) % len(question_data["answers"])
                elif event.key == pygame.K_DOWN:
                    selected = (selected + 1) % len(question_data["answers"])
                elif event.key == pygame.K_RETURN:
                    return selected

        # Render question
        screen.fill(BLACK)
        draw_text(screen, question_data["question"], (20, 20), YELLOW)
        for idx, ans in enumerate(question_data["answers"]):
            color = WHITE
            if idx == selected:
                color = YELLOW
            draw_text(screen, ans, (40, 80 + idx * 40), color)
        pygame.display.flip()
        clock.tick(15)

def select_difficulty():
    options = ["Easy", "Medium", "Hard"]
    selected = 0
    while True:
        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                pygame.quit()
                sys.exit()
            elif event.type == pygame.KEYDOWN:
                if event.key == pygame.K_UP:
                    selected = (selected - 1) % len(options)
                elif event.key == pygame.K_DOWN:
                    selected = (selected + 1) % len(options)
                elif event.key == pygame.K_RETURN:
                    return options[selected]

        screen.fill(BLACK)
        draw_text(screen, "Select Difficulty", (WIDTH // 2 - 100, HEIGHT // 2 - 60), YELLOW)
        for i, opt in enumerate(options):
            color = WHITE
            if i == selected:
                color = YELLOW
            draw_text(screen, opt, (WIDTH // 2 - 40, HEIGHT // 2 + i * 40), color)
        pygame.display.flip()
        clock.tick(15)

def save_highscore(score):
    global HIGH_SCORE
    if score > HIGH_SCORE:
        HIGH_SCORE = score
        with open(HIGHSCORE_FILE, "w") as f:
            json.dump({"score": HIGH_SCORE}, f)

def main():
    # Select difficulty at start
    difficulty = select_difficulty()
    if difficulty == "Easy":
        speed = 8
        spawn_interval = 20
    elif difficulty == "Medium":
        speed = 12
        spawn_interval = 15
    else:  # Hard
        speed = 15
        spawn_interval = 10

    global clock
    clock.tick(speed)

    snake = Snake()
    fruits = []
    score = 0
    spawn_counter = 0

    running = True
    while running:
        clock.tick(speed)
        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                running = False
            elif event.type == pygame.KEYDOWN:
                if event.key == pygame.K_UP:
                    snake.set_direction((0, -GRID_SIZE))
                elif event.key == pygame.K_DOWN:
                    snake.set_direction((0, GRID_SIZE))
                elif event.key == pygame.K_LEFT:
                    snake.set_direction((-GRID_SIZE, 0))
                elif event.key == pygame.K_RIGHT:
                    snake.set_direction((GRID_SIZE, 0))

        if not snake.move():
            running = False  # Snake hit itself

        # Check for eating fruits
        for fruit in fruits:
            if snake.positions[0] == fruit.position:
                answer_idx = ask_question(fruit.question)
                correct_idx = fruit.question["correct"]
                if answer_idx == correct_idx:
                    score += 1
                    snake.grow()
                fruits.remove(fruit)
                break

        # Spawn fruits
        spawn_counter += 1
        if spawn_counter >= spawn_interval:
            spawn_counter = 0
            # Keep 2-4 fruits
            while len(fruits) < 4:
                new_fruit = Fruit()
                if new_fruit.position not in snake.positions and all(f.position != new_fruit.position for f in fruits):
                    fruits.append(new_fruit)
                if len(fruits) >= 2:
                    break

        # Draw everything
        screen.fill(BLACK)
        snake.draw(screen)
        for f in fruits:
            f.draw(screen)
        draw_text(screen, f"Score: {score}", (10, 10))
        draw_text(screen, f"High Score: {HIGH_SCORE}", (10, 40))
        pygame.display.flip()

    # Save high score when game over
    save_highscore(score)

    # Show game over message
    screen.fill(BLACK)
    draw_text(screen, "Game Over!", (WIDTH//2 - 60, HEIGHT//2 - 20))
    draw_text(screen, f"Final Score: {score}", (WIDTH//2 - 70, HEIGHT//2 + 20))
    draw_text(screen, f"High Score: {HIGH_SCORE}", (WIDTH//2 - 70, HEIGHT//2 + 50))
    pygame.display.flip()
    pygame.time.wait(3000)
    pygame.quit()

if _name_ == "_main_":
    main()