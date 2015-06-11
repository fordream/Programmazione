#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include "conio.c"

//Struttura dei controller sui rettangoli. funzioni da implementare:
// - Crea rettangolo
// - Set colore sfondo e scritte
// - Aggiorna
// - Aggiungi scritta
// - Distruggi schermo
#define true 1
#define false 0
typedef int bool;

typedef struct Schermo {
	int xstart, ystart;
	int dimx, dimy;
	int curx, cury;
	int bkg, frg, mod;
	char *txt;
} Schermo;

Schermo* creaDisplay (int xstart, int ystart, int xend, int yend) {
	Schermo *ptr;
	int dimx = xend-xstart+1, dimy = yend-ystart+1;
	int i;

	ptr = (Schermo*) malloc(sizeof(Schermo));
	if (ptr == NULL) return NULL;
	ptr->xstart = xstart; ptr->ystart = ystart;
	ptr->dimx = dimx; ptr->dimy = dimy;
	ptr->curx = 0; ptr->cury = 0;
	ptr->txt = (char*) malloc(dimx*dimy*sizeof(char));
	if (ptr->txt == NULL) {
		free(ptr->txt);
		free(ptr);
		return NULL;
	}
	//Altrimenti tutto è andato a buon fine. Inizializziamo tutto a 0
	for (i=0; i<dimx*dimy; i++) ptr->txt[i] = ' ';
	ptr->bkg = BKG_WHITE; ptr->frg = FRG_BLACK; ptr->mod = ALLOFF;
	return ptr;
}

bool distruggiDisplay (Schermo *disp) {
	if (disp == NULL) return false;
	free(disp->txt);
	free(disp);
	return true;
}

bool setbackgroundcolor (Schermo *disp, int bkgcolor) {
	if (disp == NULL) return false;
	disp->bkg = bkgcolor;
	return true;
}

bool setforegroundcolor (Schermo *disp, int frgcolor) {
	if (disp == NULL) return false;
	disp->frg = frgcolor;
	return true;
}

bool settextmode (Schermo *disp, int textmode) {
	if (disp == NULL) return false;
	disp->mod = textmode;
	return true;
}

bool aggiornaDisplay (Schermo *disp) {
	int i, j;
	
	if (disp == NULL) return false;
	//i per le righe, j per le colonne. La matrice è arrangiata per colonne consecutive
	text(disp->bkg); text(disp->frg); text(disp->mod);
	for (i=0; i<=disp->dimy; i++) {
		for (j=0; j<=disp->dimx; j++) {
			gotoxy(j+disp->xstart,i+disp->ystart);
			printf("%c", disp->txt[i*disp->dimx+j]);
		}
	}
	text(BKG_WHITE); text(FRG_BLACK); text(ALLOFF);
	return true;
}

int aggiungiScritta (Schermo *disp, char *str) {
	int len = strlen(str), space, totspace;
	int i, j;

	if (disp == NULL) return -1;
	space = (disp->dimy - disp->cury) * disp->dimx;
	totspace = disp->dimy * disp->dimx;
	//Cerchiamo di vedere se ci sta nello spazio che abbiamo a disposizione
	//Il cursore dovrebbe essere ad inizio riga (curx = 0)
	if (len > space && len > totspace) {
		//La stringa non ci sta in tutto lo spazio. Noi scriviamo la parte che ci sta e restituiamo il numero di caratteri letti
		for (i=0; i<totspace; i++) disp->txt[i] = str[i];
		disp->cury = disp->dimy;
		disp->curx = 0;
		return totspace;
	} else if (len > space && len <= totspace) {
		//La stringa non ci sta nello spazio che avanza. Dobbiamo far scorrere le altre scritte.
	} else {
		//La stringa sta tutta nello spazio che abbiamo
	}	
	return len;
}

