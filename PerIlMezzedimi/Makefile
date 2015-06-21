# Makefile per la compilazione del progetto LaTeX
# Creato date

.PHONY: pdf dvi ps compile clean download all
compile: pdf
	make clean

all: pdf dvi ps
	make clean

ps: dvi
	dvips BasiCicliche.dvi
	dvips Errori.dvi
	dvips GeometriaAffineEuclidea.dvi

dvi: download
	latex --shell-escape BasiCicliche.tex
	latex --shell-escape Errori.tex
	latex --shell-escape GeometriaAffineEuclidea.tex

pdf: download
	pdflatex --shell-escape BasiCicliche.tex
	pdflatex --shell-escape Errori.tex
	pdflatex --shell-escape GeometriaAffineEuclidea.tex

# Dovremmo controllare che sia la versione più recente
download:
	wget https://github.com/trenta3/stdmdoc/raw/master/stdmdoc.cls -O stdmdoc.cls

clean:
	rm -f *.aux *.log *.out

# Fine del Makefile
