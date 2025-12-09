# Presentation / Report Notes for Assignment 3.2

## 1. Problem Summary
**What problem are you solving?**
Students and lifelong learners often face **information overload** and **distractions**. Switching between multiple apps for note-taking, looking up words, and timing study sessions breaks focus. Additionally, many standard tools lack **accessibility features** for auditory learners or those with reading difficulties.

**Why is your solution effective?**
The "Smart Study Assistant" consolidates all essential study tools into a single, distraction-free web application. By integrating **speech-to-text** and **instant dictionary lookups**, it automates the tedious parts of studying. The **Focus Mode** actively reduces screen clutter to maintain attention.

**Who is your target audience?**
*   University and High School students.
*   Auditory learners.
*   Individuals with learning differences (e.g., dyslexia) who benefit from text-to-speech and voice dictation.

## 2. Wireframe Walkthrough
**Design Decisions:**
*   **Aesthetic:** We chose a **"Glassmorphism"** design with a light, airy color palette (White/Light Blue) and energetic **Orange accents**. This creates a modern, premium feel that reduces visual fatigue.
*   **Navigation:** A central **Dashboard** with large, clear cards allows users to jump between tasks (Transcribe, Focus, Dictionary) instantly without getting lost in menus.
*   **Feedback:** Interactive elements like the "pulse" animation during recording provide immediate visual feedback.

**Use Cases:**
*   **The "Lecture" Scenario:** A student sits in class, opens **Transcribe**, and records the professor. The app automatically saves the text to the database.
*   **The "Deep Work" Scenario:** A student needs to write an essay. They turn on **Focus Mode**, which hides all navigation and distractions, leaving only a 25-minute countdown timer.
*   **The "Research" Scenario:** While reading, a student encounters a complex word. They type it into the **Dictionary**, getting an instant definition, and the app *automatically* saves that word to their personal vocabulary list for later review.

## 3. Prototype Plan
**What will your prototype include?**
The prototype is a fully functional web application containing:
1.  **User Authentication:** Secure Login and Signup system.
2.  **Smart Transcription:** Real-time voice-to-text that saves to a database.
3.  **Instant Dictionary:** A search tool that fetches definitions from an external API and tracks search history.
4.  **Accessibility Tools:** Text-to-Speech (Read Aloud) and High Contrast modes.
5.  **Productivity Tools:** A Pomodoro Focus Timer and PDF/TXT Export functionality.

**What code, APIs, or tools will you use?**
*   **Frontend:** HTML5, CSS3 (Custom Glassmorphism), JavaScript.
*   **Backend:** PHP (for server-side logic).
*   **Database:** MySQL (managed via MAMP) to store users, transcripts, and vocabulary.
*   **APIs:**
    *   **Web Speech API:** For native browser-based speech recognition and synthesis.
    *   **Free Dictionary API:** For fetching word definitions via JSON.
    *   **Fetch API:** For asynchronous communication between the frontend and PHP backend.
