#include <stdio.h>
#include <stdlib.h>
#include "console.c"

int main () {
	Schermo *screen = creaDisplay(1,1,10,10);
	setbackgroundcolor(screen, BKG_YELLOW);
	settextmode(screen, BOLD);
	aggiornaDisplay(screen);
	getch();
	distruggiDisplay(screen);
	return 0;
}
