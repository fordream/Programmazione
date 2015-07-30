require "csv"

# Leggiamo il file del periodo per sapere quando vanno ordinati, e teniamoci un array dei giorni giusti
periodo = CSV.read("periodo.csv", { :col_sep => "\t"}) - [[], ["Motivazione", "Dal", "Al", "Periodo SI/NO"]]

# Iniziamo dalla prima data scritta e salviamo i giorni in cui dobbiamo ordinare i giornali.
# Le condizioni scritte più in fondo sovrascrivono le altre.
giornigiusti = []
for (motivo, inizio, fine, daconsiderare) in periodo do
	raise 'Errore nel file periodo.csv: la data di inizio deve precedere quella di fine periodo' if (Date.strptime(inizio, '%d/%m/%y') <=> Date.strptime(fine, '%d/%m/%y')) == 1
	if daconsiderare == 'SI' then
		# Aggiungiamo all'array tutti i giorni compresi tra gli estremi
		st = Date.strptime(inizio, '%d/%m/%y')
		while (st <=> Date.strptime(fine, '%d/%m/%y')) < 1 do
			giornigiusti = giornigiusti | [st]
			st = st.next
		end
		puts "Condizione '#{motivo}' aggiunta: Giorni totali #{giornigiusti.size}"
	elsif daconsiderare == 'NO' then
		# Togliamo all'array tutti i giorni compresi tra gli estremi
		st = Date.strptime(inizio, '%d/%m/%y')
		while (st <=> Date.strptime(fine, '%d/%m/%y')) < 1 do
			giornigiusti = giornigiusti - [st]
			st = st.next
		end
		puts "Condizione '#{motivo}' aggiunta: Giorni totali #{giornigiusti.size}"
	else
		raise 'Errore nel file periodo.csv: la quarta colonna puo\' contenere solo SI/NO'
	end
end

# Predisponiamo un hash dove vengono scritti il nome del giornale e quante copie bisogna comprarne
giornali = {}
# Ora leggiamo gli ordini e salviamo i nomi dei giornali
ordini = CSV.read("ordine.csv", { :col_sep => "\t"}) - [[], ["Nome giornale", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato", "Domenica", "Costo per copia"]]
for (nome, lun, mar, mer, giov, ven, sab, dom, costo) in ordini do
	puts "Aggiunto conteggio giornali di #{nome}"
	giornali[nome] = 0
end
# Ora, per ogni giorno, vediamo quali giornali sono da comprare in quel giorno e li aumentiamo di uno
for data in giornigiusti do
	for g in ordini do
		if g[data.cwday] == 'X' then
			# Il giornale va acquistato
			giornali[g[0]] += 1
		end
		raise "Crocetta invalida in giornale #{g[0]}, giorno #{data.cwday}. Si possono fare solo \'X\' o lasciare vuoto" if g[data.cwday] != 'X' and g[data.cwday] != nil
	end
end

# Alla fine scriviamo in un file per ogni giornale quante volte viene acquistato e quanto costa in totale
CSV.open("ordinefinale.csv", "wb", { :col_sep => "\t"}) do |csv|
		csv << ["Nome giornale", "Numero di copie da acquistare", "Costo di ogni copia", "Costo complessivo"]
	soldiintutto = 0
	for (nome, lun, mar, mer, giov, ven, sab, dom, costo) in ordini do
		costocomplessivo = ((costo.gsub(/,/, '.').to_f)*giornali[nome])
		soldiintutto = soldiintutto + costocomplessivo
		csv << [nome, giornali[nome], costo.gsub(/,/, '.'), sprintf("%.2f", costocomplessivo)]
	end
	csv << ["TOTALE:", nil, nil, sprintf("%.2f", soldiintutto)]
end
puts 'Ordine finale salvato in ordinefinale.csv'

File.open("ordinefinale.tex", "w") do |tex|
	tex << "\\documentclass[a4paper,NoNotes]{stdmdoc}\n"
	tex << "\\begin{document}\n"
	tex << "\\autodate\n"
	tex << "\\section*{Ordini di Giornali previsti}\n"
	tex << "\\vskip 2 cm\n"
	tex << "\\noindent\\begin{tabular}{lccc}\n"
	tex << "{\\bf Nome giornale} & {\\bf Numero di copie} & {\\bf Costo per copia} & {\\bf Costo totale} \\\\ \n"
	soldiintutto = 0
	for (nome, lun, mar, mer, giov, ven, sab, dom, costo) in ordini do
		costocomplessivo = ((costo.gsub(/,/, '.').to_f)*giornali[nome])
		soldiintutto = soldiintutto + costocomplessivo
		tex << "#{nome} & #{giornali[nome]} & #{costo.gsub(/,/, '.')} & #{sprintf('%.2f', costocomplessivo)} \\\\ \n"
	end
	tex << "\\hline {\\bf TOTALI: } & & & #{sprintf('%.2f', soldiintutto)} \\\\ \n"
	tex << "\\end{tabular}\\vskip 1 cm\n"
	tex << "\\end{document}\n"
end

system("wget -O stdmdoc.cls https://raw.githubusercontent.com/trenta3/stdmdoc/master/stdmdoc.cls 2>/dev/null >/dev/null")
system("pdflatex ordinefinale.tex >/dev/null")
system("rm *.out *.aux *.log >/dev/null")
puts 'Ordine finale salvato anche in ordinefinale.tex ed in ordinefinale.pdf'

