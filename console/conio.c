#include <stdio.h>
#include <stdlib.h>

//Pulisce lo schermo a seconda di come specificato
#define CLR_DOWN '0'
#define CLR_UP '1'
#define CLR_SCREEN '2'
void erasescreen (char c) {
	printf("\033[%cJ", c);
}

void clrscr (void) {
	erasescreen (CLR_SCREEN);
}

//Sposta il cursore alla posizione (x,y)
void gotoxy (int x, int y) {
	printf("\033[%d;%df", x, y);
}

//Definizione dei colori
//Foreground colors and Background colors
#define FRG_BLACK 30
#define FRG_RED 31
#define FRG_GREEN 32
#define FRG_YELLOW 33
#define FRG_BLUE 34
#define FRG_MAGENTA 35
#define FRG_CYAN 36
#define FRG_WHITE 37
#define BKG_BLACK 40
#define BKG_RED 41
#define BKG_GREEN 42
#define BKG_YELLOW 43
#define BKG_BLUE 44
#define BKG_MAGENTA 45
#define BKG_CYAN 46
#define BKG_WHITE 47

//Definizione degli attributi del testo
#define ALLOFF 0
#define BOLD 1
#define UNDERSCORE 4
#define BLINK 5
#define REVERSE 7
#define CONCEALED 8

//Applica colori di sfondo o modalità testo
void text (int mode) {
	printf("\033[%dm", mode);
}

//Cancella tutti i caratteri nella riga a seconda di come specificato
#define CLR_RIGHT '0'
#define CLR_LEFT '1'
#define CLR_LINE '2'
void eraseline (char mode) {
	printf("\033[%cK", mode);
}

void delline (void) {
	eraseline (CLR_LINE);
}

//Salva la posizione del cursore. Si può riportare a quella corrente usando restore
void savecursor (void) {
	printf("\033[s");
}

//Riporta il cursore alla posizione salvata
void restorecursor (void) {
	printf("\033[u");
}

//Definizione possibili movimenti del cursore
#define CRS_UP 'A'
#define CRS_DOWN 'B'
#define CRS_FORWARD 'C'
#define CRS_BACKWARD 'D'

//Muove il cursore del numero specificato di righe/colonna nella direzione desiderata
void movecursor (int n, char direction) {
	printf("\033[%d%c", n, direction);
}

//Legge un carattere senza visualizzarlo e lo restituisce
char getch (void) {
      char c; // This function should return the keystroke
      system("stty raw");    // Raw input - wait for only a single keystroke
      system("stty -echo");  // Echo off
      c = getchar();
      system("stty cooked"); // Cooked input - reset
      system("stty echo");   // Echo on - Reset
      return c;
}

//Fa sparire il cursore
void nocursor (void) {
	printf("\033[?25l");
}

//Fa riapparire il cursore
void showcursor (void) {
	printf("\033[?25h");
}

//Muove la finestra di n righe (in sù se n è positivo, in giù se n è negativo)
void movewindow (int lines) {
	int i;
	char c = (lines > 0) ? 'D' : 'M';

	lines = (lines > 0) ? lines : -lines;
	for (i=0; i<lines; i++)
		printf("\033%c", c);
}

//Fa il reset delle condizioni iniziali
void reset (void) {
	printf("\033c");
}
